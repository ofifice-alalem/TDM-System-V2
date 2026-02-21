<?php

namespace App\Http\Controllers\Shared\Sales;

use App\Http\Controllers\Controller;
use App\Models\CustomerPayment;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerPaymentController extends Controller
{
    public function generatePaymentPdf(CustomerPayment $payment)
    {
        $payment->load('customer', 'salesUser');
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $methodLabels = [
            'cash' => 'نقدي',
            'transfer' => 'تحويل بنكي',
            'check' => 'شيك'
        ];

        $logoPath = public_path('images/company.png');
        $logoBase64 = null;
        if (file_exists($logoPath) && is_file($logoPath) && str_starts_with(realpath($logoPath), public_path('images'))) {
            // Resize and compress image to reduce PDF size
            $image = imagecreatefrompng($logoPath);
            if ($image) {
                $width = imagesx($image);
                $height = imagesy($image);
                // Resize to max 200px width while maintaining aspect ratio
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
        
        $companyName = $arabic->utf8Glyphs('شركة المتفوقون الأوائل للصناعات البلاستيكية');
        
        $data = [
            'paymentNumber' => $payment->payment_number,
            'date' => $payment->created_at->format('Y-m-d H:i'),
            'customerName' => $arabic->utf8Glyphs($payment->customer->name),
            'customerPhone' => $payment->customer->phone,
            'paymentMethod' => $arabic->utf8Glyphs($methodLabels[$payment->payment_method]),
            'employeeName' => $arabic->utf8Glyphs($payment->salesUser->full_name ?? 'غير متوفر'),
            'amount' => number_format($payment->amount, 0),
            'logoBase64' => $logoBase64,
            'companyName' => $companyName,
            'title' => $arabic->utf8Glyphs('إيصال قبض'),
            'labels' => [
                'customer' => $arabic->utf8Glyphs('العميل'),
                'phone' => $arabic->utf8Glyphs('الهاتف'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'paymentMethod' => $arabic->utf8Glyphs('طريقة الدفع'),
                'employee' => $arabic->utf8Glyphs('الموظف'),
                'amount' => $arabic->utf8Glyphs('المبلغ المسدد'),
                'currency' => $arabic->utf8Glyphs('دينار'),
            ]
        ];

        $pdf = Pdf::loadView('shared.sales.payment-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('compress', 1)
            ->setOption('dpi', 96);

        return $pdf->download('payment-' . $payment->payment_number . '.pdf');
    }
}
