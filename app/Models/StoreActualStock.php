<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreActualStock extends Model
{
    public $timestamps = false;
    
    protected $table = 'store_actual_stock';
    
    protected $fillable = ['store_id', 'product_id', 'quantity'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
