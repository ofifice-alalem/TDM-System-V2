<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactoryInvoice extends Model
{
    protected $fillable = [
        'invoice_number', 'keeper_id', 'status', 'notes',
        'documented_by', 'documented_at', 'stamped_image'
    ];

    protected $casts = [
        'documented_at' => 'datetime',
    ];

    public function keeper()
    {
        return $this->belongsTo(User::class, 'keeper_id');
    }

    public function documenter()
    {
        return $this->belongsTo(User::class, 'documented_by');
    }

    public function items()
    {
        return $this->hasMany(FactoryInvoiceItem::class, 'invoice_id');
    }
}
