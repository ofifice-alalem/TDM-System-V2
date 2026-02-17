<?php

namespace App\Http\Controllers\Shared\Sales;

use App\Http\Controllers\Controller;
use App\Models\CustomerInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerInvoiceController extends Controller
{
    public function generateInvoicePdf(CustomerInvoice $invoice)
    {
        $invoice->load('items.product', 'customer', 'salesUser');
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $toEnglishNumbers = function($str) {
            $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return str_replace($eastern, $western, $str);
        };

        $paymentTypeLabels = [
            'cash' => 'نقدي',
            'credit' => 'آجل',
            'partial' => 'جزئي'
        ];

        $logoPath = public_path('images/company.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
        
        $companyName = $arabic->utf8Glyphs('شركة المتفوقون الأوائل للصناعات البلاستيكية');

        $data = [
            'invoiceNumber' => $invoice->invoice_number,
            'date' => $invoice->created_at->format('Y-m-d H:i'),
            'customerName' => $arabic->utf8Glyphs($invoice->customer->name),
            'customerPhone' => $invoice->customer->phone,
            'employeeName' => $arabic->utf8Glyphs($invoice->salesUser->full_name ?? 'غير متوفر'),
            'isInvalid' => $invoice->status === 'cancelled',
            'logoBase64' => $logoBase64,
            'companyName' => $companyName,
            'items' => $invoice->items->map(function($item) use ($arabic, $toEnglishNumbers) {
                return (object)[
                    'name' => $toEnglishNumbers($arabic->utf8Glyphs($item->product->name)),
                    'quantity' => $item->quantity,
                    'unitPrice' => number_format($item->unit_price, 0),
                    'totalPrice' => number_format($item->total_price, 0)
                ];
            }),
            'subtotal' => number_format($invoice->subtotal, 0),
            'discountAmount' => number_format($invoice->discount_amount, 0),
            'totalAmount' => number_format($invoice->total_amount, 0),
            'notes' => $invoice->notes ? $arabic->utf8Glyphs($invoice->notes) : null,
            'currency' => $arabic->utf8Glyphs('دينار'),
            'title' => $arabic->utf8Glyphs('فاتورة مبيعات'),
            'labels' => [
                'customer' => $arabic->utf8Glyphs('العميل'),
                'phone' => $arabic->utf8Glyphs('الهاتف'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'employee' => $arabic->utf8Glyphs('الموظف'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'unitPrice' => $arabic->utf8Glyphs('سعر الوحدة'),
                'total' => $arabic->utf8Glyphs('الإجمالي'),
                'subtotal' => $arabic->utf8Glyphs('المجموع الفرعي'),
                'discount' => $arabic->utf8Glyphs('الخصم'),
                'grandTotal' => $arabic->utf8Glyphs('الإجمالي النهائي'),
                'notes' => $arabic->utf8Glyphs('ملاحظات'),
                'invalidInvoice' => $arabic->utf8Glyphs('الفاتورة لا يعتد بها'),
            ]
        ];

        $pdf = Pdf::loadView('shared.sales.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true);
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
