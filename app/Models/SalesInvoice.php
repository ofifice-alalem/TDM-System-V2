<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $fillable = [
        'invoice_number', 'marketer_id', 'store_id', 'total_amount', 'subtotal',
        'product_discount', 'invoice_discount_type', 'invoice_discount_value',
        'invoice_discount_amount', 'invoice_discount_tier_id', 'status', 'keeper_id', 
        'rejected_by', 'rejected_at', 'stamped_invoice_image', 'confirmed_at', 'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'product_discount' => 'decimal:2',
        'invoice_discount_value' => 'decimal:2',
        'invoice_discount_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'rejected_at' => 'datetime',
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

    public function invoiceDiscountTier()
    {
        return $this->belongsTo(InvoiceDiscountTier::class, 'invoice_discount_tier_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'invoice_id');
    }
}
