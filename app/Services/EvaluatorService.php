<?php

namespace App\Services;

use App\Models\MarketListing;

class EvaluatorService
{
    public function __construct(
        private PriceCalculator $calculator
    ) {}

    public function evaluate(string $model, string $storage, int $batteryHealth, array $conditions): array
    {
        $settings = auth()->user()->company->getSettingsOrDefault();
        $cities = config('dgifipe.cities');
        $lookbackDays = config('dgifipe.listing_lookback_days');

        $listings = MarketListing::forModel($model, $storage)
            ->inCities($cities)
            ->recent($lookbackDays)
            ->pluck('price')
            ->sort()
            ->values()
            ->toArray();

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

        $conditionDiscount = $this->calculator->calculateConditionDiscount($conditions, $settings);
        $batteryDiscount = $settings->getBatteryDepreciation($batteryHealth);
        $margin = (float) $settings->default_margin;

        $suggestedPrice = $this->calculator->applySuggestedPrice(
            $stats['average'],
            $conditionDiscount,
            $batteryDiscount,
            $margin
        );

        return [
            'market_average' => round($stats['average'], 2),
            'price_min' => round($stats['min'], 2),
            'price_max' => round($stats['max'], 2),
            'suggested_price' => round($suggestedPrice, 2),
            'listings_count' => $listingsCount,
            'low_data_warning' => $lowDataWarning,
            'condition_discount' => $conditionDiscount,
            'battery_discount' => $batteryDiscount,
            'margin' => $margin,
        ];
    }
}
