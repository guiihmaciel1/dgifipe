<?php

namespace App\Services;

use App\Models\MarketListing;
use App\Models\Simulation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class EvaluatorService
{
    public function __construct(
        private PriceCalculator $calculator
    ) {}

    public function evaluate(
        string $model,
        string $storage,
        int $batteryHealth,
        string $deviceState,
        array $accessoryChecks,
    ): array {
        $settings = auth()->user()->company->getSettingsOrDefault();
        $cities = config('dgifipe.cities');
        $lookbackDays = config('dgifipe.listing_lookback_days');

        $cacheKey = "listings:{$model}:{$storage}";
        $cacheTtl = $this->secondsUntilNextScrape();

        $listingData = Cache::remember($cacheKey, $cacheTtl, fn () => $this->fetchListingData($model, $storage, $cities, $lookbackDays));

        $listings = $listingData['prices'];
        $lastCollectedAt = $listingData['last_collected_at'];
        $listingsCount = count($listings);
        $lowDataWarning = $listingsCount < config('dgifipe.min_listings_warning');

        if ($listingsCount === 0) {
            return [
                'market_average' => null,
                'median' => null,
                'price_min' => null,
                'price_max' => null,
                'suggested_price' => null,
                'resale_price' => null,
                'listings_count' => 0,
                'low_data_warning' => true,
                'confidence' => 'low',
                'std_dev' => 0,
                'last_collected_at' => null,
                'last_evaluation' => null,
                'message' => 'Nenhum anúncio encontrado para este modelo.',
            ];
        }

        $trimmed = $this->calculator->trimOutliers($listings, config('dgifipe.trim_percentage'));
        $stats = $this->calculator->calculateStats($trimmed);

        $margin = (float) $settings->default_margin;
        $resaleMargin = (float) ($settings->resale_margin ?? config('dgifipe.default_resale_margin', 20));
        $batteryMod = $settings->getBatteryModifier($batteryHealth);
        $deviceStateMod = $settings->getDeviceStateModifier($deviceState);
        $accessoryLevel = self::resolveAccessoryLevel($accessoryChecks);
        $accessoryMod = $settings->getAccessoryModifier($accessoryLevel);

        $suggestedPrice = $this->calculator->calculateSuggestedPrice(
            $stats['average'],
            $margin,
            $batteryMod,
            $deviceStateMod,
            $accessoryMod,
        );

        $resalePrice = $this->calculator->calculateResalePrice($suggestedPrice, $resaleMargin);
        $confidence = $this->calculateConfidence($listingsCount, $stats['std_dev'], $stats['average']);
        $lastEvaluation = $this->getLastEvaluation($model, $storage);

        return [
            'market_average' => round($stats['average'], 2),
            'median' => round($stats['median'], 2),
            'price_min' => round($stats['min'], 2),
            'price_max' => round($stats['max'], 2),
            'suggested_price' => $suggestedPrice,
            'resale_price' => $resalePrice,
            'resale_margin' => $resaleMargin,
            'listings_count' => $listingsCount,
            'low_data_warning' => $lowDataWarning,
            'confidence' => $confidence,
            'std_dev' => round($stats['std_dev'], 2),
            'last_collected_at' => $lastCollectedAt,
            'last_evaluation' => $lastEvaluation,
            'margin' => $margin,
            'battery_modifier' => $batteryMod,
            'device_state_modifier' => $deviceStateMod,
            'accessory_level' => $accessoryLevel,
            'accessory_modifier' => $accessoryMod,
        ];
    }

    public static function resolveAccessoryLevel(array $checks): string
    {
        $count = count(array_filter($checks));

        return match (true) {
            $count === 0 => 'complete',
            $count === 1 => 'partial',
            default => 'none',
        };
    }

    private function fetchListingData(string $model, string $storage, array $cities, int $lookbackDays): array
    {
        $query = MarketListing::excludeSealed()
            ->forModel($model, $storage)
            ->inCities($cities)
            ->recent($lookbackDays);

        $lastCollectedAt = (clone $query)->max('collected_at');

        $prices = $query->pluck('price')->sort()->values()->toArray();

        return [
            'prices' => $prices,
            'last_collected_at' => $lastCollectedAt,
        ];
    }

    private function secondsUntilNextScrape(): int
    {
        $now = Carbon::now();
        $nextScrape = $now->copy()->setTime(3, 30, 0);

        if ($now->greaterThanOrEqualTo($nextScrape)) {
            $nextScrape->addDay();
        }

        return max($now->diffInSeconds($nextScrape), 60);
    }

    private function calculateConfidence(int $count, float $stdDev, float $average): string
    {
        if ($average <= 0) {
            return 'low';
        }

        $cv = $stdDev / $average;

        if ($count >= 10 && $cv < 0.30) {
            return 'high';
        }

        if ($count >= 5 && $cv < 0.50) {
            return 'medium';
        }

        return 'low';
    }

    private function getLastEvaluation(string $model, string $storage): ?array
    {
        $companyId = auth()->user()->company_id;

        $simulation = Simulation::whereHas('evaluationSession', fn ($q) => $q->where('company_id', $companyId))
            ->where('model', $model)
            ->where('storage', $storage)
            ->where('id', '!=', 0) // will be replaced by current after save
            ->latest()
            ->first();

        if (!$simulation) {
            return null;
        }

        $daysAgo = (int) floor($simulation->created_at->diffInDays(now()));

        return [
            'suggested_price' => $simulation->suggested_price,
            'date' => $daysAgo < 1 ? 'hoje' : $simulation->created_at->format('d/m/Y'),
            'days_ago' => $daysAgo,
        ];
    }
}
