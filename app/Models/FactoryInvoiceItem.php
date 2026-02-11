<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactoryInvoiceItem extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['invoice_id', 'product_id', 'quantity'];

    public function invoice()
    {
        return $this->belongsTo(FactoryInvoice::class, 'invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
