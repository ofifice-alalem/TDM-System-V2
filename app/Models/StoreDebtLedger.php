<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreDebtLedger extends Model
{
    public $timestamps = false;
    
    protected $table = 'store_debt_ledger';
    
    protected $fillable = [
        'store_id', 'entry_type', 'sales_invoice_id',
        'return_id', 'payment_id', 'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }
}
