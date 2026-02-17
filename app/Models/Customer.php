<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'id_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function invoices()
    {
        return $this->hasMany(CustomerInvoice::class);
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function returns()
    {
        return $this->hasMany(CustomerReturn::class);
    }

    public function debtLedger()
    {
        return $this->hasMany(CustomerDebtLedger::class);
    }

    public function getTotalDebtAttribute()
    {
        return $this->debtLedger()->sum('amount');
    }
}
