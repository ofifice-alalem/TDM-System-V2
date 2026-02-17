<?php

namespace App\Http\Controllers\Shared\Sales;

use App\Http\Controllers\Controller;
use App\Models\CustomerPayment;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerPaymentController extends Controller
{
    public function generatePaymentPdf(CustomerPayment $payment)
    {
        $payment->load('customer');
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $methodLabels = [
            'cash' => 'نقدي',
            'transfer' => 'تحويل بنكي',
            'check' => 'شيك'
        ];

        $logoPath = public_path('images/company.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
        
        $companyName = $arabic->utf8Glyphs('شركة المتفوقون الأوائل للصناعات البلاستيكية');
        
        $data = [
            'paymentNumber' => $payment->payment_number,
            'date' => $payment->created_at->format('Y-m-d H:i'),
            'customerName' => $arabic->utf8Glyphs($payment->customer->name),
            'customerPhone' => $payment->customer->phone,
            'paymentMethod' => $arabic->utf8Glyphs($methodLabels[$payment->payment_method]),
            'amount' => number_format($payment->amount, 0),
            'logoBase64' => $logoBase64,
            'companyName' => $companyName,
            'title' => $arabic->utf8Glyphs('إيصال قبض'),
            'labels' => [
                'customer' => $arabic->utf8Glyphs('العميل'),
                'phone' => $arabic->utf8Glyphs('الهاتف'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'paymentMethod' => $arabic->utf8Glyphs('طريقة الدفع'),
                'amount' => $arabic->utf8Glyphs('المبلغ المسدد'),
                'currency' => $arabic->utf8Glyphs('دينار'),
            ]
        ];

        $pdf = Pdf::loadView('shared.sales.payment-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true);

        return $pdf->download('payment-' . $payment->payment_number . '.pdf');
    }
}
