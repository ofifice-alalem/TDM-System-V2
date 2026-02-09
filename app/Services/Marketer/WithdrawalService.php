<?php

namespace App\Services\Marketer;

use App\Models\MarketerWithdrawalRequest;
use App\Models\MarketerCommission;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    public function createWithdrawal($marketerId, $amount, $notes = null)
    {
        return DB::transaction(function () use ($marketerId, $amount, $notes) {
            $available = $this->getAvailableBalance($marketerId);
            
            if ($amount > $available) {
                throw new \Exception("المبلغ المطلوب ({$amount}) أكبر من الرصيد المتاح ({$available})");
            }

            return MarketerWithdrawalRequest::create([
                'marketer_id' => $marketerId,
                'requested_amount' => $amount,
                'status' => 'pending',
                'notes' => $notes,
            ]);
        });
    }

    public function cancelWithdrawal($withdrawalId, $marketerId)
    {
        return DB::transaction(function () use ($withdrawalId, $marketerId) {
            $withdrawal = MarketerWithdrawalRequest::where('id', $withdrawalId)
                ->where('marketer_id', $marketerId)
                ->where('status', 'pending')
                ->firstOrFail();

            $withdrawal->update(['status' => 'cancelled']);
            return $withdrawal;
        });
    }

    public function getAvailableBalance($marketerId)
    {
        $totalCommissions = MarketerCommission::where('marketer_id', $marketerId)->sum('commission_amount');
        $totalWithdrawn = MarketerWithdrawalRequest::where('marketer_id', $marketerId)
            ->where('status', 'approved')
            ->sum('requested_amount');
        
        return max(0, $totalCommissions - $totalWithdrawn);
    }
}
