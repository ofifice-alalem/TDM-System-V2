<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FactoryInvoice;
use Illuminate\Http\Request;

class AdminFactoryInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = FactoryInvoice::with('items.product', 'keeper', 'documenter');

        $hasFilter = $request->filled('invoice_number');

        if (!$hasFilter && $request->filled('status')) {
            $query->where('status', $request->status);
        } elseif (!$hasFilter && !$request->has('all')) {
            $query->where('status', 'pending');
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        return view('shared.factory-invoices.index', compact('invoices'));
    }

    public function show(FactoryInvoice $factoryInvoice)
    {
        return view('shared.factory-invoices.show', [
            'invoice' => $factoryInvoice->load('items.product', 'keeper', 'documenter')
        ]);
    }

    public function pdf(FactoryInvoice $factoryInvoice)
    {
        $factoryInvoice->load('items.product', 'keeper', 'documenter', 'canceller');
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $toEnglishNumbers = function($str) {
            $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return str_replace($eastern, $western, $str);
        };

        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'documented' => 'موثق',
            'cancelled' => 'ملغى'
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
            'invoiceNumber' => $factoryInvoice->invoice_number,
            'date' => $factoryInvoice->created_at->format('Y-m-d H:i'),
            'keeperName' => $arabic->utf8Glyphs($factoryInvoice->keeper->full_name),
            'status' => $arabic->utf8Glyphs($statusLabels[$factoryInvoice->status]),
            'itemsCount' => $factoryInvoice->items->count(),
            'isInvalid' => $factoryInvoice->status === 'cancelled',
            'logoBase64' => $logoBase64,
            'companyName' => $companyName,
            'documentedBy' => $factoryInvoice->documenter ? $arabic->utf8Glyphs($factoryInvoice->documenter->full_name) : null,
            'cancelledBy' => $factoryInvoice->canceller ? $arabic->utf8Glyphs($factoryInvoice->canceller->full_name) : null,
            'totalQuantity' => $factoryInvoice->items->sum('quantity'),
            'items' => $factoryInvoice->items->map(function($item) use ($arabic, $toEnglishNumbers) {
                return (object)[
                    'name' => $toEnglishNumbers($arabic->utf8Glyphs($item->product->name)),
                    'quantity' => $item->quantity
                ];
            }),
            'title' => $arabic->utf8Glyphs('فاتورة مصنع'),
            'labels' => [
                'keeper' => $arabic->utf8Glyphs('منشئ الفاتورة'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'itemsCount' => $arabic->utf8Glyphs('عدد الأصناف'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'management' => $arabic->utf8Glyphs('توقيع الإدارة'),
                'keeperSignature' => $arabic->utf8Glyphs('أمين المخزن'),
                'documentedBy' => $arabic->utf8Glyphs('وثق بواسطة'),
                'cancelledBy' => $arabic->utf8Glyphs('ألغي بواسطة'),
                'totalQuantity' => $arabic->utf8Glyphs('إجمالي البضاعة'),
                'invalidInvoice' => $arabic->utf8Glyphs('الفاتورة لا يعتد بها'),
            ]
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shared.factory-invoices.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('compress', 1)
            ->setOption('dpi', 96);
            
        return $pdf->download('factory-invoice-' . $factoryInvoice->invoice_number . '.pdf');
    }
}
