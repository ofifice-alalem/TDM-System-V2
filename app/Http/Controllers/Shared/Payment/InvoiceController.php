<?php

namespace App\Http\Controllers\Shared\Payment;

use App\Http\Controllers\Controller;
use App\Models\StorePayment;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function generatePaymentInvoicePdf(StorePayment $payment)
    {
        $payment->load('store', 'marketer', 'keeper');
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'موثق',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغي'
        ];

        $methodLabels = [
            'cash' => 'نقدي',
            'transfer' => 'تحويل بنكي',
            'certified_check' => 'شيك مصدق'
        ];
        
        $data = [
            'paymentNumber' => $payment->payment_number,
            'date' => $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : '',
            'marketerName' => $arabic->utf8Glyphs($payment->marketer->full_name ?? 'غير متوفر'),
            'storeName' => $arabic->utf8Glyphs($payment->store->name ?? 'غير متوفر'),
            'keeperName' => $arabic->utf8Glyphs($payment->keeper->full_name ?? 'غير متوفر'),
            'status' => $arabic->utf8Glyphs($statusLabels[$payment->status] ?? 'غير محدد'),
            'paymentMethod' => $arabic->utf8Glyphs($methodLabels[$payment->payment_method] ?? 'غير محدد'),
            'amount' => number_format($payment->amount, 2),
            'isInvalid' => in_array($payment->status, ['cancelled', 'rejected']),
            'title' => $arabic->utf8Glyphs('إيصال قبض'),
            'labels' => [
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'store' => $arabic->utf8Glyphs('المتجر'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'paymentMethod' => $arabic->utf8Glyphs('طريقة الدفع'),
                'amount' => $arabic->utf8Glyphs('المبلغ المسدد'),
                'invalidPayment' => $arabic->utf8Glyphs('ملغي'),
                'currency' => $arabic->utf8Glyphs('دينار'),
            ]
        ];

        $pdf = Pdf::loadView('shared.payments.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true);

        return $pdf->download('payment-' . $payment->payment_number . '.pdf');
    }
}
