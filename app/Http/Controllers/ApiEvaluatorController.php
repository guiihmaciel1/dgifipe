<?php

namespace App\Http\Controllers;

use App\Models\MarketListing;
use App\Services\PriceCalculator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiEvaluatorController extends Controller
{
    public function __construct(
        private PriceCalculator $calculator,
    ) {}

    public function models(): JsonResponse
    {
        return response()->json([
            'models' => config('dgifipe.models'),
        ]);
    }

    public function listings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model' => ['required', 'string'],
            'storage' => ['required', 'string'],
        ]);

        $data = $this->getCachedListingData($validated['model'], $validated['storage']);

        return response()->json([
            'prices' => $data['prices'],
            'last_collected_at' => $data['last_collected_at'],
            'listings_count' => count($data['prices']),
        ]);
    }

    public function evaluate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model' => ['required', 'string'],
            'storage' => ['required', 'string'],
            'battery_health' => ['required', 'integer', 'min:0', 'max:100'],
            'device_state' => ['required', 'string', 'in:original,repaired'],
            'no_box' => ['boolean'],
            'no_cable' => ['boolean'],
        ]);

        $data = $this->getCachedListingData($validated['model'], $validated['storage']);
        $listings = $data['prices'];
        $listingsCount = count($listings);

        if ($listingsCount === 0) {
            return response()->json([
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
            ]);
        }

        $trimmed = $this->calculator->trimOutliers($listings, config('dgifipe.trim_percentage'));
        $stats = $this->calculator->calculateStats($trimmed);

        $margin = (float) config('dgifipe.default_margin');
        $resaleMargin = (float) config('dgifipe.default_resale_margin');
        $batteryMod = $this->getBatteryModifier((int) $validated['battery_health']);
        $deviceStateMod = $this->getDeviceStateModifier($validated['device_state']);

        $accessoryChecks = [
            'no_box' => ! empty($validated['no_box']),
            'no_cable' => ! empty($validated['no_cable']),
        ];
        $accessoryLevel = $this->resolveAccessoryLevel($accessoryChecks);
        $accessoryMod = $this->getAccessoryModifier($accessoryLevel);

        $suggestedPrice = $this->calculator->calculateSuggestedPrice(
            $stats['median'], $margin, $batteryMod, $deviceStateMod, $accessoryMod,
        );

        $resalePrice = floor($stats['median'] / 100) * 100;

        return response()->json([
            'market_average' => round($stats['average'], 2),
            'median' => round($stats['median'], 2),
            'price_min' => round($stats['min'], 2),
            'price_max' => round($stats['max'], 2),
            'suggested_price' => $suggestedPrice,
            'resale_price' => $resalePrice,
            'listings_count' => $listingsCount,
            'low_data_warning' => $listingsCount < config('dgifipe.min_listings_warning'),
            'confidence' => $this->calculateConfidence($listingsCount, $stats['std_dev'], $stats['average']),
            'std_dev' => round($stats['std_dev'], 2),
            'last_collected_at' => $data['last_collected_at'],
            'margin' => $margin,
            'battery_modifier' => $batteryMod,
            'device_state_modifier' => $deviceStateMod,
            'accessory_level' => $accessoryLevel,
            'accessory_modifier' => $accessoryMod,
        ]);
    }

    private function getCachedListingData(string $model, string $storage): array
    {
        $cacheKey = "api:listings:{$model}:{$storage}";
        $cacheTtl = $this->secondsUntilNextScrape();

        return Cache::remember($cacheKey, $cacheTtl, function () use ($model, $storage) {
            $cities = config('dgifipe.cities');
            $lookbackDays = config('dgifipe.listing_lookback_days');

            $query = MarketListing::excludeSealed()
                ->forModel($model, $storage)
                ->inCities($cities)
                ->recent($lookbackDays);

            $lastCollectedAt = (clone $query)->max('collected_at');
            $prices = $query->pluck('price')->sort()->values()->toArray();

            return [
                'prices' => array_map('floatval', $prices),
                'last_collected_at' => $lastCollectedAt,
            ];
        });
    }

    private function secondsUntilNextScrape(): int
    {
        $now = Carbon::now();
        $nextScrape = $now->copy()->setTime(3, 30, 0);

        if ($now->greaterThanOrEqualTo($nextScrape)) {
            $nextScrape->addDay();
        }

        return max((int) $now->diffInSeconds($nextScrape), 60);
    }

    private function resolveAccessoryLevel(array $checks): string
    {
        $count = count(array_filter($checks));

        return match (true) {
            $count === 0 => 'complete',
            $count === 1 => 'partial',
            default => 'none',
        };
    }

    private function getBatteryModifier(int $health): float
    {
        foreach (config('dgifipe.default_battery_rules') as $rule) {
            if ($health >= $rule['min'] && $health <= $rule['max']) {
                return (float) $rule['modifier'];
            }
        }

        return -25.0;
    }

    private function getDeviceStateModifier(string $state): float
    {
        return (float) (config('dgifipe.default_device_state_options')[$state] ?? 0);
    }

    private function getAccessoryModifier(string $level): float
    {
        return (float) (config('dgifipe.default_accessory_options')[$level] ?? 0);
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
}
