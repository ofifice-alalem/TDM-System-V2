<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreDebtLedger extends Model
{
    public $timestamps = false;
    
    protected $table = 'store_debt_ledger';
    
    protected $fillable = [
        'store_id', 'entry_type', 'sales_invoice_id',
        'return_id', 'payment_id', 'amount', 'balance_after', 'created_at', 'marketer_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class, 'return_id');
    }

    public function storePayment()
    {
        return $this->belongsTo(StorePayment::class, 'payment_id');
    }

    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }
}
