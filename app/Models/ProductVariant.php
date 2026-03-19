<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'name', 'sku', 'price', 'stock', 'attributes'];

    protected $casts = [
        'attributes' => 'array',
        'price' => 'decimal:0',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
