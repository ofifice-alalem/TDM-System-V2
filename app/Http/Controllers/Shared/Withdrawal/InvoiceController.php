<?php

namespace App\Http\Controllers\Shared\Withdrawal;

use App\Http\Controllers\Controller;
use App\Models\MarketerWithdrawalRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function generateWithdrawalInvoicePdf(MarketerWithdrawalRequest $withdrawal)
    {
        $withdrawal->load('marketer', 'approvedByUser', 'rejectedByUser');
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغي'
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
            'withdrawalNumber' => $withdrawal->id,
            'date' => $withdrawal->created_at ? $withdrawal->created_at->format('Y-m-d H:i') : '',
            'marketerName' => $arabic->utf8Glyphs($withdrawal->marketer->full_name ?? 'غير متوفر'),
            'status' => $arabic->utf8Glyphs($statusLabels[$withdrawal->status] ?? 'غير محدد'),
            'amount' => number_format($withdrawal->requested_amount, 2),
            'approvedBy' => $withdrawal->approvedByUser ? $arabic->utf8Glyphs($withdrawal->approvedByUser->full_name ?? $withdrawal->approvedByUser->name) : null,
            'rejectedBy' => $withdrawal->rejectedByUser ? $arabic->utf8Glyphs($withdrawal->rejectedByUser->full_name ?? $withdrawal->rejectedByUser->name) : null,
            'notes' => $withdrawal->notes ? $arabic->utf8Glyphs($withdrawal->notes) : null,
            'isInvalid' => in_array($withdrawal->status, ['cancelled', 'rejected']),
            'logoBase64' => $logoBase64,
            'companyName' => $arabic->utf8Glyphs('شركة المتفوقون الأوائل للصناعات البلاستيكية'),
            'title' => $arabic->utf8Glyphs('طلب سحب أرباح'),
            'labels' => [
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'amount' => $arabic->utf8Glyphs('المبلغ المطلوب'),
                'approvedBy' => $arabic->utf8Glyphs('تمت الموافقة بواسطة'),
                'rejectedBy' => $arabic->utf8Glyphs('تم الرفض بواسطة'),
                'notes' => $arabic->utf8Glyphs('ملاحظات'),
                'invalidWithdrawal' => $arabic->utf8Glyphs('الفاتورة لا يعتد بها'),
                'currency' => $arabic->utf8Glyphs('دينار'),
                'marketerSignature' => $arabic->utf8Glyphs('توقيع المسوق'),
                'adminSignature' => $arabic->utf8Glyphs('توقيع الإدارة'),
            ]
        ];

        $pdf = Pdf::loadView('shared.withdrawals.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('compress', 1)
            ->setOption('dpi', 96);

        return $pdf->download('withdrawal-' . $withdrawal->id . '.pdf');
    }
}
