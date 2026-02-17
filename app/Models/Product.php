<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'barcode', 'description', 'current_price', 'customer_price', 'is_active'];

    protected $casts = [
        'current_price' => 'decimal:2',
        'customer_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function mainStock()
    {
        return $this->hasOne(MainStock::class);
    }

    public function salesInvoiceItems()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function promotions()
    {
        return $this->hasMany(ProductPromotion::class);
    }

    public function activePromotion()
    {
        return $this->hasOne(ProductPromotion::class)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest();
    }
}
