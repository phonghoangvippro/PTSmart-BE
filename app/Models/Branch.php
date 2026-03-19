<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'lat', 'lng'];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];
}
