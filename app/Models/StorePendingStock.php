<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorePendingStock extends Model
{
    public $timestamps = false;
    
    protected $table = 'store_pending_stock';
    
    protected $fillable = ['store_id', 'sales_invoice_id', 'product_id', 'quantity'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
