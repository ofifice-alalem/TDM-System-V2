<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorePayment extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'payment_number', 'store_id', 'marketer_id', 'keeper_id',
        'amount', 'payment_method', 'status', 'receipt_image',
        'confirmed_at', 'notes', 'created_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

    public function keeper()
    {
        return $this->belongsTo(User::class, 'keeper_id');
    }
}
