<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainStock extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'product_id';
    protected $table = 'main_stock';

    protected $fillable = ['product_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
