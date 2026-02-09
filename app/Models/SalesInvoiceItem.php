<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'invoice_id', 'product_id', 'quantity', 'free_quantity',
        'unit_price', 'total_price', 'promotion_id'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function promotion()
    {
        return $this->belongsTo(ProductPromotion::class, 'promotion_id');
    }
}
