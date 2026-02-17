<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerReturnItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'return_id',
        'invoice_item_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function return()
    {
        return $this->belongsTo(CustomerReturn::class, 'return_id');
    }

    public function invoiceItem()
    {
        return $this->belongsTo(CustomerInvoiceItem::class, 'invoice_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
