<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'excerpt', 'thumbnail',
        'category', 'read_time', 'is_featured', 'author_id',
        'view_count', 'status', 'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'integer',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 1)->whereNotNull('published_at');
    }
}
