<?php

namespace App\Services\Admin;

use App\Models\MarketerWithdrawalRequest;
use Illuminate\Support\Facades\DB;

class AdminWithdrawalService
{
    public function approveWithdrawal($withdrawalId, $adminId, $receiptImage)
    {
        return DB::transaction(function () use ($withdrawalId, $adminId, $receiptImage) {
            $withdrawal = MarketerWithdrawalRequest::where('id', $withdrawalId)
                ->where('status', 'pending')
                ->firstOrFail();

            $withdrawal->update([
                'status' => 'approved',
                'approved_by' => $adminId,
                'approved_at' => now(),
                'signed_receipt_image' => $receiptImage,
            ]);

            return $withdrawal->fresh();
        });
    }

    public function rejectWithdrawal($withdrawalId, $adminId, $notes)
    {
        return DB::transaction(function () use ($withdrawalId, $adminId, $notes) {
            $withdrawal = MarketerWithdrawalRequest::where('id', $withdrawalId)
                ->where('status', 'pending')
                ->firstOrFail();

            $withdrawal->update([
                'status' => 'rejected',
                'rejected_by' => $adminId,
                'rejected_at' => now(),
                'notes' => $notes,
            ]);

            return $withdrawal;
        });
    }
}
