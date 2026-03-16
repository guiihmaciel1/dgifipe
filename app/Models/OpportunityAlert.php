<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpportunityAlert extends Model
{
    protected $fillable = [
        'model',
        'storage',
        'listing_price',
        'market_average',
        'suggested_buy_price',
        'potential_profit',
        'profit_percentage',
        'source',
        'city',
        'title',
        'url',
        'status',
        'notified_at',
    ];

    protected $casts = [
        'listing_price' => 'decimal:2',
        'market_average' => 'decimal:2',
        'suggested_buy_price' => 'decimal:2',
        'potential_profit' => 'decimal:2',
        'profit_percentage' => 'decimal:2',
        'notified_at' => 'datetime',
    ];

    public function scopeUnread($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeNotDismissed($query)
    {
        return $query->whereIn('status', ['new', 'viewed']);
    }
}
