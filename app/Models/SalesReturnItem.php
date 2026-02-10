<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'return_id', 'sales_invoice_item_id', 'product_id', 'quantity', 'unit_price'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class, 'return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function salesInvoiceItem()
    {
        return $this->belongsTo(SalesInvoiceItem::class);
    }
}
