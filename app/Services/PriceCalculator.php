<?php

namespace App\Services;

use App\Models\CompanySetting;

class PriceCalculator
{
    public function trimOutliers(array $sortedPrices, int $percentage): array
    {
        $count = count($sortedPrices);
        if ($count <= 4) {
            return $sortedPrices;
        }

        $trimCount = (int) floor($count * ($percentage / 100));
        return array_slice($sortedPrices, $trimCount, $count - ($trimCount * 2));
    }

    public function calculateStats(array $prices): array
    {
        if (empty($prices)) {
            return ['average' => 0, 'median' => 0, 'min' => 0, 'max' => 0];
        }

        $count = count($prices);
        $average = array_sum($prices) / $count;
        $min = min($prices);
        $max = max($prices);

        sort($prices);
        $middle = (int) floor($count / 2);
        $median = ($count % 2 === 0)
            ? ($prices[$middle - 1] + $prices[$middle]) / 2
            : $prices[$middle];

        return compact('average', 'median', 'min', 'max');
    }

    public function calculateConditionDiscount(array $conditions, CompanySetting $settings): float
    {
        $total = 0.0;

        foreach ($conditions as $condition) {
            $total += $settings->getConditionDiscount($condition);
        }

        return $total;
    }

    public function applySuggestedPrice(
        float $marketAverage,
        float $conditionDiscount,
        float $batteryDiscount,
        float $margin
    ): float {
        return $marketAverage
            * (1 - ($conditionDiscount / 100))
            * (1 - ($batteryDiscount / 100))
            * (1 - ($margin / 100));
    }
}
