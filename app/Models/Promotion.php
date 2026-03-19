<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    protected $fillable = ['title', 'description', 'image', 'type', 'start_at', 'end_at', 'status'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'status' => 'integer',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promotion_products');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }
}
