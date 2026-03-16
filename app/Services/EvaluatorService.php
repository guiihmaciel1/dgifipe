<?php

namespace App\Services;

use App\Models\MarketListing;
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
        $listings = Cache::remember($cacheKey, 900, fn () =>
            MarketListing::excludeSealed()
                ->forModel($model, $storage)
                ->inCities($cities)
                ->recent($lookbackDays)
                ->pluck('price')
                ->sort()
                ->values()
                ->toArray()
        );

        $listingsCount = count($listings);
        $lowDataWarning = $listingsCount < config('dgifipe.min_listings_warning');

        if ($listingsCount === 0) {
            return [
                'market_average' => null,
                'price_min' => null,
                'price_max' => null,
                'suggested_price' => null,
                'listings_count' => 0,
                'low_data_warning' => true,
                'message' => 'Nenhum anúncio encontrado para este modelo.',
            ];
        }

        $trimmed = $this->calculator->trimOutliers($listings, config('dgifipe.trim_percentage'));
        $stats = $this->calculator->calculateStats($trimmed);

        $margin = (float) $settings->default_margin;
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

        return [
            'market_average' => round($stats['average'], 2),
            'price_min' => round($stats['min'], 2),
            'price_max' => round($stats['max'], 2),
            'suggested_price' => $suggestedPrice,
            'listings_count' => $listingsCount,
            'low_data_warning' => $lowDataWarning,
            'margin' => $margin,
            'battery_modifier' => $batteryMod,
            'device_state_modifier' => $deviceStateMod,
            'accessory_level' => $accessoryLevel,
            'accessory_modifier' => $accessoryMod,
        ];
    }

    /**
     * Nenhum check marcado = completo, 1 = parcial, 2 = nenhum
     */
    public static function resolveAccessoryLevel(array $checks): string
    {
        $count = count(array_filter($checks));

        return match (true) {
            $count === 0 => 'complete',
            $count === 1 => 'partial',
            default => 'none',
        };
    }
}
