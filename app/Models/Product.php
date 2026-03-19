<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'specifications',
        'category_id', 'brand_id', 'price', 'sale_price',
        'stock', 'thumbnail', 'rating_avg', 'review_count',
        'sold_count', 'is_featured', 'status',
    ];

    protected $casts = [
        'specifications' => 'array',
        'price' => 'decimal:0',
        'sale_price' => 'decimal:0',
        'rating_avg' => 'decimal:1',
        'is_featured' => 'boolean',
        'status' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getCurrentPriceAttribute(): string
    {
        return $this->sale_price ?? $this->price;
    }
}
