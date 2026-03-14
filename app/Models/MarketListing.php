<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    protected static function booted(): void
    {
        static::addGlobalScope('exclude_contradictions', function ($query) {
            $query->whereRaw(self::contradictionFilter());
        });
    }

    public function scopeExcludeSealed($query)
    {
        return $query->where(
            'title',
            'NOT REGEXP',
            '(lacrado|lacrada|selado|selada|sealed|novo na caixa|zero na caixa)'
        );
    }

    /**
     * Build SQL that keeps a listing only when title does NOT
     * mention a different iPhone model or a different storage.
     *
     * Strategy (from most specific to least, to avoid "iPhone 11"
     * matching inside "iPhone 11 Pro Max"):
     *  1. Extract the most specific model from the title via ordered CASE.
     *  2. Compare against the `model` column.
     *  3. Same for storage.
     * If title is empty or has no iPhone reference, the row passes.
     */
    private static function contradictionFilter(): string
    {
        $modelDetect = self::buildTitleModelDetector();
        $storageDetect = self::buildTitleStorageDetector();

        return "(
            title = ''
            OR LOWER(title) NOT LIKE '%iphone%'
            OR (
                (({$modelDetect}) IS NULL OR ({$modelDetect}) = LOWER(model))
                AND
                (({$storageDetect}) IS NULL OR ({$storageDetect}) = LOWER(storage))
            )
        )";
    }

    /**
     * CASE expression that returns the most specific iPhone model
     * found in the title, or NULL. Ordered longest-first so
     * "iPhone 11 Pro Max" is tested before "iPhone 11 Pro" and "iPhone 11".
     */
    private static function buildTitleModelDetector(): string
    {
        $models = array_keys(config('dgifipe.models', []));
        usort($models, fn ($a, $b) => strlen($b) - strlen($a));

        $whens = [];
        foreach ($models as $m) {
            $lower = strtolower($m);
            $pattern = str_replace(' ', ' ?', preg_quote($lower, '/'));
            $escaped = addslashes($pattern);
            $val = addslashes($lower);
            $whens[] = "WHEN LOWER(title) REGEXP '{$escaped}' THEN '{$val}'";
        }

        return 'CASE ' . implode(' ', $whens) . ' ELSE NULL END';
    }

    /**
     * CASE expression that returns the storage mentioned in the
     * title (e.g. '128gb', '1tb'), or NULL if none found.
     * Checks from largest to smallest to avoid partial matches.
     */
    private static function buildTitleStorageDetector(): string
    {
        $storages = ['1tb', '512gb', '256gb', '128gb', '64gb'];

        $whens = [];
        foreach ($storages as $s) {
            $num = rtrim($s, 'gbt');
            $unit = str_replace($num, '', $s);
            $pattern = "{$num} ?{$unit}";
            $whens[] = "WHEN LOWER(title) REGEXP '{$pattern}' THEN '{$s}'";
        }

        return 'CASE ' . implode(' ', $whens) . ' ELSE NULL END';
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
