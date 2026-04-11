<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_id',
        'default_margin',
        'resale_margin',
        'depreciation_rules',
        'condition_discounts',
        'battery_rules',
        'device_state_options',
        'accessory_options',
        'allow_concurrent_sessions',
        'session_lifetime_days',
    ];

    protected $casts = [
        'default_margin' => 'decimal:2',
        'resale_margin' => 'decimal:2',
        'depreciation_rules' => 'array',
        'condition_discounts' => 'array',
        'battery_rules' => 'array',
        'device_state_options' => 'array',
        'accessory_options' => 'array',
        'allow_concurrent_sessions' => 'boolean',
        'session_lifetime_days' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getBatteryModifier(int $health): float
    {
        $rules = $this->battery_rules ?? config('dgifipe.default_battery_rules');

        foreach ($rules as $rule) {
            if ($health >= $rule['min'] && $health <= $rule['max']) {
                return (float) $rule['modifier'];
            }
        }

        return -25.0;
    }

    public function getDeviceStateModifier(string $state): float
    {
        $options = $this->device_state_options ?? config('dgifipe.default_device_state_options');

        return (float) ($options[$state] ?? 0);
    }

    public function getAccessoryModifier(string $level): float
    {
        $options = $this->accessory_options ?? config('dgifipe.default_accessory_options');

        return (float) ($options[$level] ?? 0);
    }

    public function getBatteryRules(): array
    {
        return $this->battery_rules ?? config('dgifipe.default_battery_rules');
    }

    public function getDeviceStateOptions(): array
    {
        return $this->device_state_options ?? config('dgifipe.default_device_state_options');
    }

    public function getAccessoryOptions(): array
    {
        return $this->accessory_options ?? config('dgifipe.default_accessory_options');
    }
}
