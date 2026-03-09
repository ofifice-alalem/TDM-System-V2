<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name', 'owner_name', 'phone', 'location', 'address', 'is_active', 'marketer_id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

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
        return $this->debtLedger()
            ->latest('id')
            ->value('balance_after') ?? 0;
    }
}
