<?php

namespace App\Http\Controllers\Shared\Sales;

use App\Http\Controllers\Controller;
use App\Models\CustomerReturn;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerReturnController extends Controller
{
    public function generateReturnPdf(CustomerReturn $return)
    {
        $return->load('items.product', 'customer', 'invoice', 'salesUser');
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $toEnglishNumbers = function($str) {
            $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return str_replace($eastern, $western, $str);
        };

        $logoPath = public_path('images/company.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
        
        $companyName = $arabic->utf8Glyphs('شركة المتفوقون الأوائل للصناعات البلاستيكية');

        $data = [
            'returnNumber' => $return->return_number,
            'date' => $return->created_at->format('Y-m-d H:i'),
            'customerName' => $arabic->utf8Glyphs($return->customer->name),
            'customerPhone' => $return->customer->phone,
            'invoiceNumber' => $return->invoice->invoice_number,
            'employeeName' => $arabic->utf8Glyphs($return->salesUser->full_name ?? 'غير متوفر'),
            'isInvalid' => $return->status === 'cancelled',
            'logoBase64' => $logoBase64,
            'companyName' => $companyName,
            'items' => $return->items->map(function($item) use ($arabic, $toEnglishNumbers) {
                return (object)[
                    'name' => $toEnglishNumbers($arabic->utf8Glyphs($item->product->name)),
                    'quantity' => $item->quantity,
                    'unitPrice' => number_format($item->unit_price, 0),
                    'totalPrice' => number_format($item->total_price, 0)
                ];
            }),
            'totalAmount' => number_format($return->total_amount, 0),
            'currency' => $arabic->utf8Glyphs('دينار'),
            'title' => $arabic->utf8Glyphs('مرتجعات'),
            'labels' => [
                'customer' => $arabic->utf8Glyphs('العميل'),
                'phone' => $arabic->utf8Glyphs('الهاتف'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'invoiceNumber' => $arabic->utf8Glyphs('رقم الفاتورة الأصلية'),
                'employee' => $arabic->utf8Glyphs('الموظف'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'unitPrice' => $arabic->utf8Glyphs('سعر الوحدة'),
                'total' => $arabic->utf8Glyphs('الإجمالي'),
                'grandTotal' => $arabic->utf8Glyphs('الإجمالي النهائي'),
                'invalidReturn' => $arabic->utf8Glyphs('المرتجع لا يعتد به'),
            ]
        ];

        $pdf = Pdf::loadView('shared.sales.return-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true);
        return $pdf->download('return-' . $return->return_number . '.pdf');
    }
}
