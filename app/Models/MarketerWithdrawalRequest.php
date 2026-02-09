<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketerWithdrawalRequest extends Model
{
    protected $fillable = [
        'marketer_id', 'requested_amount', 'status', 'approved_by',
        'approved_at', 'rejected_by', 'rejected_at', 'signed_receipt_image', 'notes'
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
