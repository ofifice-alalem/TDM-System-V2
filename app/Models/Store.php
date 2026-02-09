<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name', 'owner_name', 'phone', 'location', 'address', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function debtLedger()
    {
        return $this->hasMany(StoreDebtLedger::class);
    }

    public function getTotalDebtAttribute()
    {
        return $this->debtLedger()->sum('amount');
    }
}
