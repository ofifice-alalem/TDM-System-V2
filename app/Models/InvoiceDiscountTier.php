<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDiscountTier extends Model
{
    protected $fillable = [
        'min_amount',
        'discount_type',
        'discount_percentage',
        'discount_amount',
        'start_date',
        'end_date',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
