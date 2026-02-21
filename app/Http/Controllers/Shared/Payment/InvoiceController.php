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
        
        $logoPath = public_path('images/company.png');
        $logoBase64 = null;
        if (file_exists($logoPath) && is_file($logoPath) && str_starts_with(realpath($logoPath), public_path('images'))) {
            $image = imagecreatefrompng($logoPath);
            if ($image) {
                $width = imagesx($image);
                $height = imagesy($image);
                $maxWidth = 200;
                if ($width > $maxWidth) {
                    $newWidth = $maxWidth;
                    $newHeight = ($height / $width) * $newWidth;
                    $resized = imagecreatetruecolor($newWidth, $newHeight);
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
                    imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($image);
                    $image = $resized;
                }
                imagealphablending($image, false);
                imagesavealpha($image, true);
                ob_start();
                imagepng($image, null, 9);
                $compressedImage = ob_get_clean();
                imagedestroy($image);
                $logoBase64 = base64_encode($compressedImage);
            } else {
                $logoBase64 = base64_encode(file_get_contents($logoPath));
            }
        }
        
        $data = [
            'paymentNumber' => $payment->payment_number,
            'date' => $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : '',
            'marketerName' => $arabic->utf8Glyphs($payment->marketer->full_name ?? 'غير متوفر'),
            'storeName' => $arabic->utf8Glyphs($payment->store->name ?? 'غير متوفر'),
            'keeperName' => $payment->keeper ? $arabic->utf8Glyphs($payment->keeper->full_name) : null,
            'confirmedDate' => $payment->confirmed_at ? $payment->confirmed_at->format('Y-m-d H:i') : null,
            'status' => $arabic->utf8Glyphs($statusLabels[$payment->status] ?? 'غير محدد'),
            'statusValue' => $payment->status,
            'paymentMethod' => $arabic->utf8Glyphs($methodLabels[$payment->payment_method] ?? 'غير محدد'),
            'amount' => number_format($payment->amount, 2),
            'isInvalid' => in_array($payment->status, ['cancelled', 'rejected']),
            'logoBase64' => $logoBase64,
            'companyName' => $arabic->utf8Glyphs('شركة المتفوقون الأوائل للصناعات البلاستيكية'),
            'title' => $arabic->utf8Glyphs('إيصال قبض'),
            'labels' => [
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'store' => $arabic->utf8Glyphs('المتجر'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'approvedBy' => $arabic->utf8Glyphs('وثق بواسطة'),
                'rejectedBy' => $arabic->utf8Glyphs('رفض بواسطة'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'paymentMethod' => $arabic->utf8Glyphs('طريقة الدفع'),
                'amount' => $arabic->utf8Glyphs('المبلغ المسدد'),
                'invalidPayment' => $arabic->utf8Glyphs('الفاتورة لا يعتد بها'),
                'currency' => $arabic->utf8Glyphs('دينار'),
            ]
        ];

        $pdf = Pdf::loadView('shared.payments.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('compress', 1)
            ->setOption('dpi', 96);

        return $pdf->download('payment-' . $payment->payment_number . '.pdf');
    }
}
