<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketerReservedStock extends Model
{
    protected $table = 'marketer_reserved_stock';
    
    protected $fillable = ['marketer_id', 'product_id', 'quantity'];

    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
