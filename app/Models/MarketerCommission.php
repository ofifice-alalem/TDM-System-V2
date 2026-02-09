<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketerCommission extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'marketer_id', 'store_id', 'keeper_id', 'payment_amount',
        'payment_id', 'commission_rate', 'commission_amount'
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function keeper()
    {
        return $this->belongsTo(User::class, 'keeper_id');
    }

    public function payment()
    {
        return $this->belongsTo(StorePayment::class, 'payment_id');
    }
}
