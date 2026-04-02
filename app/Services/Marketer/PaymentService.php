<?php

namespace App\Services\Marketer;

use App\Models\StorePayment;
use App\Models\StoreDebtLedger;
use App\Models\MarketerCommission;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function createPayment($marketerId, $storeId, $amount, $paymentMethod, $notes = null)
    {
        return DB::transaction(function () use ($marketerId, $storeId, $amount, $paymentMethod, $notes) {
            $currentDebt = $this->getStoreDebt($storeId);
            
            if ($amount <= 0) {
                throw new \Exception('المبلغ يجب أن يكون أكبر من صفر');
            }

            return StorePayment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'store_id' => $storeId,
                'marketer_id' => $marketerId,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'status' => 'pending',
                'notes' => $notes,
            ]);
        });
    }

    public function adjustPayment($paymentId, $newAmount, $newMethod, $notes)
    {
        return DB::transaction(function () use ($paymentId, $newAmount, $newMethod, $notes) {
            $payment = StorePayment::findOrFail($paymentId);
            $oldAmount = $payment->amount;

            $payment->update([
                'amount'         => $newAmount,
                'payment_method' => $newMethod,
                'notes'          => $notes,
            ]);

            if ($payment->status === 'approved' && $oldAmount != $newAmount) {
                $ledger = StoreDebtLedger::where('payment_id', $paymentId)->first();
                if ($ledger) {
                    $diff = $oldAmount - $newAmount;
                    $ledger->update([
                        'amount'        => -$newAmount,
                        'balance_after' => $ledger->balance_after + $diff,
                    ]);
                }

                $commission = MarketerCommission::where('payment_id', $paymentId)->first();
                if ($commission) {
                    $commission->update([
                        'payment_amount'    => $newAmount,
                        'commission_amount' => $newAmount * ($commission->commission_rate / 100),
                    ]);
                }
            }
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
        return StoreDebtLedger::where('store_id', $storeId)
            ->latest('id')
            ->value('balance_after') ?? 0;
    }

    private function generatePaymentNumber()
    {
        return 'RCP-' . date('Ymd') . '-' . str_pad(StorePayment::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
