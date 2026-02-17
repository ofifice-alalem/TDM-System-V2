<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDebtLedger extends Model
{
    protected $table = 'customer_debt_ledger';

    protected $fillable = [
        'customer_id',
        'entry_type',
        'invoice_id',
        'return_id',
        'payment_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(CustomerInvoice::class, 'invoice_id');
    }

    public function return()
    {
        return $this->belongsTo(CustomerReturn::class, 'return_id');
    }

    public function payment()
    {
        return $this->belongsTo(CustomerPayment::class, 'payment_id');
    }
}
