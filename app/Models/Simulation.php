<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Simulation extends Model
{
    protected $fillable = [
        'evaluation_session_id',
        'model',
        'storage',
        'battery_health',
        'conditions',
        'market_average',
        'price_min',
        'price_max',
        'suggested_price',
        'listings_count',
        'low_data_warning',
    ];

    protected $casts = [
        'conditions' => 'array',
        'market_average' => 'decimal:2',
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'suggested_price' => 'decimal:2',
        'battery_health' => 'integer',
        'listings_count' => 'integer',
        'low_data_warning' => 'boolean',
    ];

    public function evaluationSession(): BelongsTo
    {
        return $this->belongsTo(EvaluationSession::class);
    }
}
