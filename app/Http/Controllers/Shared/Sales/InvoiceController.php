<?php

namespace App\Http\Controllers\Shared\Sales;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function generateSalesInvoicePdf(SalesInvoice $sale)
    {
        $sale->load('items.product', 'store', 'marketer');
        
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

        $totalWithGifts = $sale->subtotal + $sale->product_discount;
        
        $logoPath = public_path('images/company.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
        
        $data = [
            'invoiceNumber' => $sale->invoice_number,
            'date' => $sale->created_at ? $sale->created_at->format('Y-m-d H:i') : '',
            'marketerName' => $arabic->utf8Glyphs($sale->marketer->full_name ?? 'غير متوفر'),
            'storeName' => $arabic->utf8Glyphs($sale->store->name ?? 'غير متوفر'),
            'status' => $arabic->utf8Glyphs($statusLabels[$sale->status] ?? 'غير محدد'),
            'isInvalid' => in_array($sale->status, ['cancelled', 'rejected']),
            'logoBase64' => $logoBase64,
            'companyName' => $arabic->utf8Glyphs('شركة تقنية للتوزيع'),
            'customerName' => $arabic->utf8Glyphs($sale->store->name ?? 'غير متوفر'),
            'customerPhone' => $sale->store->phone ?? 'غير متوفر',
            'employeeName' => $arabic->utf8Glyphs($sale->marketer->full_name ?? 'غير متوفر'),
            'subtotal' => number_format($totalWithGifts, 2),
            'productDiscount' => number_format($sale->product_discount, 2),
            'invoiceDiscount' => number_format($sale->invoice_discount_amount, 2),
            'currency' => 'د',
            'totalAmount' => number_format($sale->total_amount, 2),
            'items' => $sale->items->map(function($item) use ($arabic, $toEnglishNumbers) {
                $totalQty = $item->quantity + $item->free_quantity;
                $totalPrice = $totalQty * $item->unit_price;
                return (object)[
                    'name' => $toEnglishNumbers($arabic->utf8Glyphs($item->product->name ?? 'غير متوفر')),
                    'quantity' => $item->quantity,
                    'freeQuantity' => $item->free_quantity,
                    'totalQuantity' => $totalQty,
                    'unitPrice' => number_format($item->unit_price, 2),
                    'totalPrice' => number_format($totalPrice, 2)
                ];
            }),
            'title' => $arabic->utf8Glyphs('فاتورة بيع'),
            'labels' => [
                'customer' => $arabic->utf8Glyphs('العميل'),
                'phone' => $arabic->utf8Glyphs('الهاتف'),
                'employee' => $arabic->utf8Glyphs('المسوق'),
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'store' => $arabic->utf8Glyphs('المتجر'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'unitPrice' => $arabic->utf8Glyphs('سعر الوحدة'),
                'free' => $arabic->utf8Glyphs('التخفيض'),
                'price' => $arabic->utf8Glyphs('السعر'),
                'total' => $arabic->utf8Glyphs('الإجمالي'),
                'subtotal' => $arabic->utf8Glyphs('السعر الكلي'),
                'discount' => $arabic->utf8Glyphs('التخفيض'),
                'productDiscount' => $arabic->utf8Glyphs('تخفيض المنتجات'),
                'invoiceDiscount' => $arabic->utf8Glyphs('تخفيض الفاتورة'),
                'grandTotal' => $arabic->utf8Glyphs('المجموع النهائي'),
                'finalTotal' => $arabic->utf8Glyphs('المجموع النهائي'),
                'invalidInvoice' => $arabic->utf8Glyphs('الفاتورة لا يعتد بها'),
                'currency' => $arabic->utf8Glyphs('دينار'),
            ]
        ];

        $pdf = Pdf::loadView('shared.sales.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true);

        return $pdf->download('invoice-' . $sale->invoice_number . '.pdf');
    }
}
