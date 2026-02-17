<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'sales_user_id',
        'subtotal',
        'discount_amount',
        'total_amount',
        'payment_type',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesUser()
    {
        return $this->belongsTo(User::class, 'sales_user_id');
    }

    public function items()
    {
        return $this->hasMany(CustomerInvoiceItem::class, 'invoice_id');
    }

    public function returns()
    {
        return $this->hasMany(CustomerReturn::class, 'invoice_id');
    }
}
