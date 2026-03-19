<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
    protected $fillable = ['title', 'start_at', 'end_at', 'status'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'status' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 1)
            ->where('start_at', '>', now());
    }
}
