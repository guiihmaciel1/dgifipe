<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Company $company) {
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(CompanySetting::class);
    }

    public function evaluationSessions(): HasMany
    {
        return $this->hasMany(EvaluationSession::class);
    }

    public function getSettingsOrDefault(): CompanySetting
    {
        return $this->settings ?? new CompanySetting([
            'default_margin' => config('dgifipe.default_margin', 15),
            'resale_margin' => config('dgifipe.default_resale_margin', 20),
            'battery_rules' => config('dgifipe.default_battery_rules'),
            'device_state_options' => config('dgifipe.default_device_state_options'),
            'accessory_options' => config('dgifipe.default_accessory_options'),
        ]);
    }
}
