<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'method', 'amount', 'status',
        'transaction_id', 'paid_at', 'response_data',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'paid_at' => 'datetime',
        'response_data' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
