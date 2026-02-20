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

        $data = [
            'invoiceNumber' => $factoryInvoice->invoice_number,
            'date' => $factoryInvoice->created_at->format('Y-m-d H:i'),
            'keeperName' => $arabic->utf8Glyphs($factoryInvoice->keeper->full_name),
            'status' => $arabic->utf8Glyphs($statusLabels[$factoryInvoice->status]),
            'itemsCount' => $factoryInvoice->items->count(),
            'totalQuantity' => $factoryInvoice->items->sum('quantity'),
            'isInvalid' => $factoryInvoice->status === 'cancelled',
            'items' => $factoryInvoice->items->map(function($item) use ($arabic, $toEnglishNumbers) {
                return (object)[
                    'name' => $toEnglishNumbers($arabic->utf8Glyphs($item->product->name)),
                    'quantity' => $item->quantity
                ];
            }),
            'title' => $arabic->utf8Glyphs('فاتورة مصنع'),
            'labels' => [
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'itemsCount' => $arabic->utf8Glyphs('عدد الأصناف'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'totalQuantity' => $arabic->utf8Glyphs('إجمالي الكمية'),
                'piece' => $arabic->utf8Glyphs('قطعة'),
                'management' => $arabic->utf8Glyphs('توقيع الإدارة'),
                'keeper' => $arabic->utf8Glyphs('توقيع أمين المخزن'),
                'invalidInvoice' => $arabic->utf8Glyphs('ملغى'),
            ]
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shared.factory-invoices.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true);
            
        return $pdf->download('factory-invoice-' . $factoryInvoice->invoice_number . '.pdf');
    }
}
