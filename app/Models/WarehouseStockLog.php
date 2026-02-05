<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStockLog extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['invoice_type', 'invoice_id', 'keeper_id', 'action'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function keeper()
    {
        return $this->belongsTo(User::class, 'keeper_id');
    }
}
