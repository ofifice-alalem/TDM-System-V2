<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $fillable = [
        'return_number', 'sales_invoice_id', 'store_id', 'marketer_id',
        'total_amount', 'status', 'keeper_id', 'stamped_image',
        'confirmed_at', 'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function keeper()
    {
        return $this->belongsTo(User::class, 'keeper_id');
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class, 'return_id');
    }
}
