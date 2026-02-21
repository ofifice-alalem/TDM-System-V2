<?php

namespace App\Http\Controllers\Shared\SalesReturn;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function generateSalesReturnInvoicePdf(SalesReturn $salesReturn)
    {
        $salesReturn->load(['store', 'marketer', 'salesInvoice', 'items.product', 'keeper']);
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $toEnglishNumbers = function($str) {
            $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return str_replace($eastern, $western, $str);
        };

        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'موثق',
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
        
        $companyName = $arabic->utf8Glyphs('شركة المتفوقون الأوائل للصناعات البلاستيكية');
        
        $data = [
            'returnNumber' => $salesReturn->return_number,
            'invoiceNumber' => $salesReturn->salesInvoice->invoice_number,
            'date' => $salesReturn->created_at ? $salesReturn->created_at->format('Y-m-d H:i') : '',
            'marketerName' => $arabic->utf8Glyphs($salesReturn->marketer->full_name ?? 'غير متوفر'),
            'storeName' => $arabic->utf8Glyphs($salesReturn->store->name ?? 'غير متوفر'),
            'status' => $arabic->utf8Glyphs($statusLabels[$salesReturn->status] ?? 'غير محدد'),
            'statusValue' => $salesReturn->status,
            'keeperName' => $salesReturn->keeper ? $arabic->utf8Glyphs($salesReturn->keeper->full_name) : null,
            'confirmedDate' => $salesReturn->confirmed_at ? $salesReturn->confirmed_at->format('Y-m-d H:i') : null,
            'isInvalid' => in_array($salesReturn->status, ['cancelled', 'rejected']),
            'logoBase64' => $logoBase64,
            'companyName' => $companyName,
            'totalAmount' => number_format($salesReturn->total_amount, 2),
            'items' => $salesReturn->items->map(function($item) use ($arabic, $toEnglishNumbers) {
                $totalPrice = $item->quantity * $item->unit_price;
                return (object)[
                    'name' => $toEnglishNumbers($arabic->utf8Glyphs($item->product->name ?? 'غير متوفر')),
                    'quantity' => $item->quantity,
                    'unit_price' => number_format($item->unit_price, 2),
                    'total_price' => number_format($totalPrice, 2)
                ];
            }),
            'title' => $arabic->utf8Glyphs('إرجاع بضاعة'),
            'labels' => [
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'store' => $arabic->utf8Glyphs('المتجر'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'approvedBy' => $arabic->utf8Glyphs('وثق بواسطة'),
                'rejectedBy' => $arabic->utf8Glyphs('رفض بواسطة'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'price' => $arabic->utf8Glyphs('السعر'),
                'total' => $arabic->utf8Glyphs('الإجمالي'),
                'finalTotal' => $arabic->utf8Glyphs('الإجمالي'),
                'invalidInvoice' => $arabic->utf8Glyphs('الفاتورة لا يعتد بها'),
                'currency' => $arabic->utf8Glyphs('دينار'),
                'originalInvoice' => $arabic->utf8Glyphs('الفاتورة الأصلية'),
            ]
        ];

        $pdf = Pdf::loadView('shared.sales-returns.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('compress', 1)
            ->setOption('dpi', 96);
        
        return $pdf->download('sales-return-' . $salesReturn->return_number . '.pdf');
    }
}
