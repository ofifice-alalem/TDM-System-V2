<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketerReturnRequest extends Model
{
    protected $fillable = [
        'invoice_number', 'marketer_id', 'status', 'notes',
        'approved_by', 'approved_at', 'rejected_by', 'rejected_at',
        'documented_by', 'documented_at', 'stamped_image'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'documented_at' => 'datetime',
    ];

    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

    public function items()
    {
        return $this->hasMany(MarketerReturnItem::class, 'return_request_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function documenter()
    {
        return $this->belongsTo(User::class, 'documented_by');
    }
}
