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
        
        $data = [
            'withdrawalNumber' => $withdrawal->id,
            'date' => $withdrawal->created_at ? $withdrawal->created_at->format('Y-m-d H:i') : '',
            'marketerName' => $arabic->utf8Glyphs($withdrawal->marketer->full_name ?? 'غير متوفر'),
            'status' => $arabic->utf8Glyphs($statusLabels[$withdrawal->status] ?? 'غير محدد'),
            'amount' => number_format($withdrawal->requested_amount, 2),
            'approvedBy' => $withdrawal->approvedByUser ? $arabic->utf8Glyphs($withdrawal->approvedByUser->name) : null,
            'rejectedBy' => $withdrawal->rejectedByUser ? $arabic->utf8Glyphs($withdrawal->rejectedByUser->name) : null,
            'notes' => $withdrawal->notes ? $arabic->utf8Glyphs($withdrawal->notes) : null,
            'isInvalid' => in_array($withdrawal->status, ['cancelled', 'rejected']),
            'title' => $arabic->utf8Glyphs('طلب سحب أرباح'),
            'labels' => [
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'amount' => $arabic->utf8Glyphs('المبلغ المطلوب'),
                'approvedBy' => $arabic->utf8Glyphs('تمت الموافقة بواسطة'),
                'rejectedBy' => $arabic->utf8Glyphs('تم الرفض بواسطة'),
                'notes' => $arabic->utf8Glyphs('ملاحظات'),
                'invalidWithdrawal' => $arabic->utf8Glyphs('ملغي'),
                'currency' => $arabic->utf8Glyphs('دينار'),
                'marketerSignature' => $arabic->utf8Glyphs('توقيع المسوق'),
                'adminSignature' => $arabic->utf8Glyphs('توقيع الإدارة'),
            ]
        ];

        $pdf = Pdf::loadView('shared.withdrawals.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true);

        return $pdf->download('withdrawal-' . $withdrawal->id . '.pdf');
    }
}
