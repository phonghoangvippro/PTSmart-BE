<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'title', 'description', 'type', 'discount_value',
        'min_order', 'max_discount', 'usage_limit', 'used_count',
        'expired_at', 'category', 'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:0',
        'min_order' => 'decimal:0',
        'max_discount' => 'decimal:0',
        'expired_at' => 'datetime',
        'status' => 'integer',
    ];

    public function userCoupons(): HasMany
    {
        return $this->hasMany(UserCoupon::class);
    }

    public function isValid(): bool
    {
        return $this->status === 1
            && $this->expired_at->isFuture()
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->min_order) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return min($this->discount_value, $subtotal);
        }

        if ($this->type === 'percent') {
            $discount = $subtotal * ($this->discount_value / 100);
            return $this->max_discount ? min($discount, $this->max_discount) : $discount;
        }

        if ($this->type === 'shipping') {
            return $this->discount_value;
        }

        return 0;
    }
}
