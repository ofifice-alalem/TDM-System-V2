<?php

namespace App\Http\Controllers\Shared\Request;

use App\Http\Controllers\Controller;
use App\Models\MarketerRequest;
use App\Models\MarketerReturnRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function generateRequestPdf(MarketerRequest $request)
    {
        $request->load('items.product', 'marketer', 'approver', 'rejecter');
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $toEnglishNumbers = function($str) {
            $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return str_replace($eastern, $western, $str);
        };

        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'تمت الموافقة',
            'documented' => 'موثق',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغي'
        ];

        $data = [
            'invoiceNumber' => $request->invoice_number,
            'date' => $request->created_at->format('Y-m-d H:i'),
            'marketerName' => $arabic->utf8Glyphs($request->marketer->full_name),
            'status' => $arabic->utf8Glyphs($statusLabels[$request->status]),
            'approvedBy' => $request->approver ? $arabic->utf8Glyphs($request->approver->full_name) : null,
            'rejectedBy' => $request->rejecter ? $arabic->utf8Glyphs($request->rejecter->full_name) : null,
            'isInvalid' => in_array($request->status, ['rejected', 'cancelled']),
            'items' => $request->items->map(function($item) use ($arabic, $toEnglishNumbers) {
                return (object)[
                    'name' => $toEnglishNumbers($arabic->utf8Glyphs($item->product->name)),
                    'quantity' => $item->quantity
                ];
            }),
            'title' => $arabic->utf8Glyphs('طلب بضاعة'),
            'labels' => [
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'approvedBy' => $arabic->utf8Glyphs('اعتمد بواسطة'),
                'rejectedBy' => $arabic->utf8Glyphs('رفض بواسطة'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'total' => $arabic->utf8Glyphs('الإجمالي'),
                'invalidInvoice' => $arabic->utf8Glyphs('الفاتورة لا يعتد بها'),
            ]
        ];

        $pdf = Pdf::loadView('shared.requests.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true);
        return $pdf->download('request-' . $request->invoice_number . '.pdf');
    }

    public function generateReturnPdf(MarketerReturnRequest $return)
    {
        $return->load('items.product', 'marketer', 'approver', 'rejecter');
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $toEnglishNumbers = function($str) {
            $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return str_replace($eastern, $western, $str);
        };

        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'تمت الموافقة',
            'documented' => 'موثق',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغي'
        ];

        $data = [
            'invoiceNumber' => $return->invoice_number,
            'date' => $return->created_at->format('Y-m-d H:i'),
            'marketerName' => $arabic->utf8Glyphs($return->marketer->full_name),
            'status' => $arabic->utf8Glyphs($statusLabels[$return->status]),
            'approvedBy' => $return->approver ? $arabic->utf8Glyphs($return->approver->full_name) : null,
            'rejectedBy' => $return->rejecter ? $arabic->utf8Glyphs($return->rejecter->full_name) : null,
            'isInvalid' => in_array($return->status, ['rejected', 'cancelled']),
            'items' => $return->items->map(function($item) use ($arabic, $toEnglishNumbers) {
                return (object)[
                    'name' => $toEnglishNumbers($arabic->utf8Glyphs($item->product->name)),
                    'quantity' => $item->quantity
                ];
            }),
            'title' => $arabic->utf8Glyphs('إرجاع بضاعة'),
            'labels' => [
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'approvedBy' => $arabic->utf8Glyphs('اعتمد بواسطة'),
                'rejectedBy' => $arabic->utf8Glyphs('رفض بواسطة'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'total' => $arabic->utf8Glyphs('الإجمالي'),
                'invalidInvoice' => $arabic->utf8Glyphs('الفاتورة لا يعتد بها'),
            ]
        ];

        $pdf = Pdf::loadView('shared.returns.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true);
        return $pdf->download('return-' . $return->invoice_number . '.pdf');
    }
}
