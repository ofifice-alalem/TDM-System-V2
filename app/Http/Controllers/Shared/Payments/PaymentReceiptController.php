<?php

namespace App\Http\Controllers\Shared\Payments;

use App\Http\Controllers\Controller;
use App\Models\StorePayment;

class PaymentReceiptController extends Controller
{
    public function getPaymentData(StorePayment $payment)
    {
        $payment->load('store', 'marketer');
        
        $formatNumber = fn($num) => $num == floor($num) ? number_format($num, 0) : number_format($num, 2);
        
        $paymentMethodLabels = [
            'cash' => 'نقدي',
            'transfer' => 'تحويل بنكي',
            'check' => 'شيك مصدق'
        ];
        
        return response()->json([
            'payment_number' => $payment->payment_number,
            'date' => $payment->created_at->format('Y-m-d'),
            'store' => $payment->store->name,
            'store_phone' => $payment->store->phone ?? '---',
            'marketer' => $payment->marketer->full_name,
            'marketer_phone' => $payment->marketer->phone ?? '---',
            'amount' => $formatNumber($payment->amount),
            'payment_method' => $paymentMethodLabels[$payment->payment_method] ?? $payment->payment_method,
            'notes' => $payment->notes ?? ''
        ]);
    }
}
