<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreReturnPendingStock extends Model
{
    protected $table = 'store_return_pending_stock';
    
    public $timestamps = false;

    protected $fillable = [
        'return_id', 'store_id', 'product_id', 'quantity'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class, 'return_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
