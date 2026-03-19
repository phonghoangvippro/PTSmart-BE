<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title', 'subtitle', 'image', 'link', 'position', 'sort_order', 'status'];

    protected $casts = ['status' => 'integer'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
