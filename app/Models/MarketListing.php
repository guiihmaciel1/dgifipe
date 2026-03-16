<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketListing extends Model
{
    protected $fillable = [
        'model',
        'storage',
        'price',
        'city',
        'source',
        'title',
        'url',
        'screenshot_path',
        'collected_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'collected_at' => 'date',
    ];

    public function scopeExcludeSealed($query)
    {
        return $query->where(
            'title',
            'NOT REGEXP',
            '(lacrado|lacrada|selado|selada|sealed|novo na caixa|zero na caixa)'
        );
    }

    public function scopeForModel($query, string $model, string $storage)
    {
        return $query->where('model', $model)->where('storage', $storage);
    }

    public function scopeInCities($query, array $cities)
    {
        return $query->whereIn('city', $cities);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('collected_at', '>=', now()->subDays($days));
    }
}
