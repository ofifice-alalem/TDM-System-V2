<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'barcode', 'description', 'current_price', 'is_active'];

    protected $casts = [
        'current_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
