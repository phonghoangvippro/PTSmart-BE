<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code', 'user_id', 'address_id', 'subtotal',
        'discount', 'shipping_fee', 'total', 'status',
        'payment_method', 'note', 'coupon_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:0',
        'discount' => 'decimal:0',
        'shipping_fee' => 'decimal:0',
        'total' => 'decimal:0',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public static function generateOrderCode(): string
    {
        $date = now()->format('ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return 'PTS-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
