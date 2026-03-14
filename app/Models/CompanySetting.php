<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_id',
        'default_margin',
        'depreciation_rules',
        'condition_discounts',
    ];

    protected $casts = [
        'default_margin' => 'decimal:2',
        'depreciation_rules' => 'array',
        'condition_discounts' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getConditionDiscount(string $condition): float
    {
        $discounts = $this->condition_discounts ?? config('dgifipe.default_condition_discounts');
        return (float) ($discounts[$condition] ?? 0);
    }

    public function getBatteryDepreciation(int $health): float
    {
        $rules = $this->depreciation_rules ?? config('dgifipe.default_depreciation_rules');

        foreach ($rules as $rule) {
            if ($health >= $rule['min'] && $health <= $rule['max']) {
                return (float) $rule['discount'];
            }
        }

        return 12.0;
    }
}
