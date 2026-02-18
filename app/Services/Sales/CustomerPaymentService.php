<?php

namespace App\Services\Sales;

use App\Models\CustomerPayment;
use App\Models\CustomerDebtLedger;
use Illuminate\Support\Facades\DB;

class CustomerPaymentService
{
    public function createPayment($salesUserId, $customerId, $amount, $paymentMethod, $notes = null)
    {
        return DB::transaction(function () use ($salesUserId, $customerId, $amount, $paymentMethod, $notes) {
            $currentDebt = CustomerDebtLedger::where('customer_id', $customerId)->sum('amount');

            if ($amount > $currentDebt) {
                throw new \Exception("المبلغ المدفوع أكبر من الدين الحالي ({$currentDebt})");
            }

            $payment = CustomerPayment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'customer_id' => $customerId,
                'sales_user_id' => $salesUserId,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'notes' => $notes,
            ]);

            CustomerDebtLedger::create([
                'customer_id' => $customerId,
                'entry_type' => 'payment',
                'payment_id' => $payment->id,
                'amount' => -$amount,
            ]);

            return $payment->load('customer', 'salesUser');
        });
    }

    public function cancelPayment(CustomerPayment $payment, $cancelNotes = null)
    {
        return DB::transaction(function () use ($payment, $cancelNotes) {
            if ($payment->status === 'cancelled') {
                throw new \Exception('هذه الدفعة ملغاة بالفعل');
            }

            $payment->update([
                'status' => 'cancelled',
                'notes' => $cancelNotes ? ($payment->notes ? $payment->notes . '\n\n[إلغاء]: ' . $cancelNotes : '[إلغاء]: ' . $cancelNotes) : $payment->notes
            ]);

            CustomerDebtLedger::create([
                'customer_id' => $payment->customer_id,
                'entry_type' => 'payment',
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
            ]);

            return $payment;
        });
    }

    private function generatePaymentNumber()
    {
        return 'CP-' . date('Ymd') . '-' . str_pad(CustomerPayment::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
