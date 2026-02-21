<?php

namespace App\Services\Warehouse;

use App\Models\StorePayment;
use App\Models\StoreDebtLedger;
use App\Models\MarketerCommission;
use Illuminate\Support\Facades\DB;

class WarehousePaymentService
{
    public function approvePayment($paymentId, $keeperId, $receiptImage)
    {
        return DB::transaction(function () use ($paymentId, $keeperId, $receiptImage) {
            $payment = StorePayment::where('id', $paymentId)
                ->where('status', 'pending')
                ->with('marketer')
                ->firstOrFail();

            $payment->update([
                'status' => 'approved',
                'keeper_id' => $keeperId,
                'receipt_image' => $receiptImage,
                'confirmed_at' => now(),
            ]);

            StoreDebtLedger::create([
                'store_id' => $payment->store_id,
                'entry_type' => 'payment',
                'payment_id' => $payment->id,
                'amount' => -$payment->amount,
            ]);

            $commissionRate = $payment->marketer->commission_rate ?? 0;
            if ($commissionRate > 0) {
                MarketerCommission::create([
                    'marketer_id' => $payment->marketer_id,
                    'store_id' => $payment->store_id,
                    'keeper_id' => $keeperId,
                    'payment_amount' => $payment->amount,
                    'payment_id' => $payment->id,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $payment->amount * ($commissionRate / 100),
                ]);
            }

            return $payment->fresh();
        });
    }

    public function rejectPayment($paymentId, $keeperId, $notes)
    {
        return DB::transaction(function () use ($paymentId, $keeperId, $notes) {
            $payment = StorePayment::where('id', $paymentId)
                ->where('status', 'pending')
                ->firstOrFail();

            $payment->update([
                'status' => 'rejected',
                'keeper_id' => $keeperId,
                'confirmed_at' => now(),
                'notes' => $notes,
            ]);

            return $payment;
        });
    }
}
