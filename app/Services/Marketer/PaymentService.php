<?php

namespace App\Services\Marketer;

use App\Models\StorePayment;
use App\Models\StoreDebtLedger;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function createPayment($marketerId, $storeId, $keeperId, $amount, $paymentMethod, $notes = null)
    {
        return DB::transaction(function () use ($marketerId, $storeId, $keeperId, $amount, $paymentMethod, $notes) {
            $currentDebt = $this->getStoreDebt($storeId);
            
            if ($amount > $currentDebt) {
                throw new \Exception("المبلغ المدخل ({$amount}) أكبر من الدين الحالي ({$currentDebt})");
            }

            return StorePayment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'store_id' => $storeId,
                'marketer_id' => $marketerId,
                'keeper_id' => $keeperId,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'status' => 'pending',
                'notes' => $notes,
            ]);
        });
    }

    public function cancelPayment($paymentId, $marketerId)
    {
        return DB::transaction(function () use ($paymentId, $marketerId) {
            $payment = StorePayment::where('id', $paymentId)
                ->where('marketer_id', $marketerId)
                ->where('status', 'pending')
                ->firstOrFail();

            $payment->update(['status' => 'cancelled']);
            return $payment;
        });
    }

    private function getStoreDebt($storeId)
    {
        return StoreDebtLedger::where('store_id', $storeId)->sum('amount');
    }

    private function generatePaymentNumber()
    {
        return 'RCP-' . date('Ymd') . '-' . str_pad(StorePayment::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
