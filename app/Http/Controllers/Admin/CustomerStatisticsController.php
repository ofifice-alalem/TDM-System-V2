<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Sales\StatisticsController;
use App\Models\CustomerInvoice;
use App\Models\CustomerPayment;
use App\Models\CustomerReturn;
use App\Models\Feature;
use Illuminate\Http\Request;

class CustomerStatisticsController extends StatisticsController
{
    protected function viewPrefix(): string { return 'admin.customer-statistics'; }

    public function invoiceData(CustomerInvoice $invoice)
    {
        $invoice->load('items.product', 'customer', 'salesUser');
        $logoPath   = public_path('images/company.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        return response()->json([
            'operation'      => 'invoices',
            'status'         => $invoice->status,
            'date'           => $invoice->created_at->format('Y-m-d H:i'),
            'invoice_number' => $invoice->invoice_number,
            'store'          => $invoice->customer->name ?? '-',
            'store_phone'    => $invoice->customer->phone ?? '-',
            'marketer'       => $invoice->salesUser->full_name ?? '-',
            'subtotal'       => number_format($invoice->subtotal, 2),
            'product_discount' => number_format(0, 2),
            'invoice_discount' => number_format($invoice->discount_amount ?? 0, 2),
            'total'          => number_format($invoice->total_amount, 2),
            'total_items'    => $invoice->items->sum('quantity'),
            'logo_base64'    => $logoBase64,
            'items'          => $invoice->items->map(fn($i) => [
                'name'     => $i->product->name ?? '-',
                'quantity' => $i->quantity,
                'price'    => number_format($i->unit_price, 2),
                'total'    => number_format($i->total_price, 2),
            ]),
        ]);
    }

    public function paymentData(CustomerPayment $payment)
    {
        $payment->load('customer', 'salesUser');
        $logoPath   = public_path('images/company.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        $methodLabels = ['cash' => 'نقدي', 'transfer' => 'تحويل بنكي', 'check' => 'شيك'];

        return response()->json([
            'operation'      => 'payments',
            'status'         => $payment->status === 'completed' ? 'approved' : $payment->status,
            'date'           => $payment->created_at->format('Y-m-d H:i'),
            'payment_number' => $payment->payment_number,
            'store'          => $payment->customer->name ?? '-',
            'store_phone'    => $payment->customer->phone ?? '-',
            'marketer'       => $payment->salesUser->full_name ?? '-',
            'payment_method' => $methodLabels[$payment->payment_method] ?? '-',
            'amount'         => number_format($payment->amount, 2),
            'logo_base64'    => $logoBase64,
        ]);
    }

    public function returnData(CustomerReturn $return)
    {
        $return->load('items.product', 'customer', 'invoice', 'salesUser');
        $logoPath   = public_path('images/company.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        return response()->json([
            'operation'      => 'sales_returns',
            'status'         => $return->status === 'completed' ? 'approved' : $return->status,
            'date'           => $return->created_at->format('Y-m-d H:i'),
            'return_number'  => $return->return_number,
            'invoice_number' => $return->invoice->invoice_number ?? '-',
            'store'          => $return->customer->name ?? '-',
            'store_phone'    => $return->customer->phone ?? '-',
            'marketer'       => $return->salesUser->full_name ?? '-',
            'total'          => number_format($return->total_amount, 2),
            'logo_base64'    => $logoBase64,
            'items'          => $return->items->map(fn($i) => [
                'name'       => $i->product->name ?? '-',
                'quantity'   => $i->quantity,
                'unit_price' => number_format($i->unit_price, 2),
                'total_price'=> number_format($i->total_price, 2),
            ]),
        ]);
    }

    public function bulkInvoicesCount(Request $request)
    {
        $results = $this->getResultsForBulk($request);
        return response()->json(['count' => $results ? count($results['data']) : 0]);
    }

    public function bulkInvoicesPdf(Request $request)
    {
        $offset = max(0, (int) $request->input('offset', 0));
        $limit  = min(70, max(50, (int) $request->input('limit', 0))) ?: null;

        $results = $this->getResultsForBulk($request);
        if (!$results || empty($results['data'])) abort(404, 'لا توجد بيانات');

        $arabic = new \ArPHP\I18N\Arabic();
        $g = fn($t) => $arabic->utf8Glyphs($t);

        $logoPath   = public_path('images/company.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
        $companyName = $g('شركة المتفوقون الأوائل للصناعات البلاستيكية');

        $statusLabels  = ['completed' => 'مكتمل', 'cancelled' => 'ملغي'];
        $methodLabels  = ['cash' => 'نقدي', 'transfer' => 'تحويل بنكي', 'check' => 'شيك'];
        $operation     = $results['operation'];

        $items = collect($results['data']);
        if ($limit) $items = $items->slice($offset, $limit)->values();

        $invoices = $items->map(function ($item) use ($operation, $g, $statusLabels, $methodLabels, $logoBase64, $companyName) {
            $status    = $statusLabels[$item->status] ?? $item->status;
            $isInvalid = $item->status === 'cancelled';
            $pdfOperation = match($operation) {
                'invoices' => 'sales',
                'returns'  => 'sales_returns',
                default    => $operation,
            };

            $base = [
                'operation'   => $pdfOperation,
                'status'      => $g($status),
                'statusValue' => $item->status === 'completed' ? 'approved' : $item->status,
                'isInvalid'   => $isInvalid,
                'date'        => $item->created_at->format('Y-m-d H:i'),
                'logoBase64'  => $logoBase64,
                'companyName' => $companyName,
            ];

            if ($operation === 'invoices') {
                $item->loadMissing('items.product', 'customer', 'salesUser');
                return array_merge($base, [
                    'invoiceNumber' => $item->invoice_number,
                    'title'         => $g('فاتورة مبيعات'),
                    'storeName'     => $g($item->customer->name ?? '-'),
                    'storePhone'    => $item->customer->phone ?? '-',
                    'marketerName'  => $g($item->salesUser->full_name ?? '-'),
                    'keeperName'    => null,
                    'rejectedByName'=> null,
                    'confirmedDate' => null,
                    'rejectedDate'  => null,
                    'subtotal'      => number_format($item->subtotal, 2),
                    'productDiscount' => 0,
                    'invoiceDiscount' => number_format($item->discount_amount ?? 0, 2),
                    'totalAmount'   => number_format($item->total_amount, 2),
                    'totalProducts' => $item->items->sum('quantity'),
                    'items'         => $item->items->map(fn($i) => (object)[
                        'name'          => $g($i->product->name ?? '-'),
                        'quantity'      => $i->quantity,
                        'freeQuantity'  => 0,
                        'totalQuantity' => $i->quantity,
                        'unitPrice'     => number_format($i->unit_price, 2),
                        'totalPrice'    => number_format($i->total_price, 2),
                    ]),
                ]);
            }

            if ($operation === 'payments') {
                $item->loadMissing('customer', 'salesUser');
                return array_merge($base, [
                    'paymentNumber' => $item->payment_number,
                    'title'         => $g('إيصال قبض'),
                    'storeName'     => $g($item->customer->name ?? '-'),
                    'marketerName'  => $g($item->salesUser->full_name ?? '-'),
                    'keeperName'    => null,
                    'confirmedDate' => null,
                    'paymentMethod' => $g($methodLabels[$item->payment_method] ?? '-'),
                    'amount'        => number_format($item->amount, 2),
                ]);
            }

            if ($operation === 'returns') {
                $item->loadMissing('items.product', 'customer', 'invoice', 'salesUser');
                return array_merge($base, [
                    'returnNumber'  => $item->return_number,
                    'invoiceNumber' => $item->invoice->invoice_number ?? '-',
                    'title'         => $g('مرتجع عميل'),
                    'storeName'     => $g($item->customer->name ?? '-'),
                    'marketerName'  => $g($item->salesUser->full_name ?? '-'),
                    'keeperName'    => null,
                    'confirmedDate' => null,
                    'totalAmount'   => number_format($item->total_amount, 2),
                    'items'         => $item->items->map(fn($i) => (object)[
                        'name'       => $g($i->product->name ?? '-'),
                        'quantity'   => $i->quantity,
                        'unit_price' => number_format($i->unit_price, 2),
                        'total_price'=> number_format($i->total_price, 2),
                        // للتوافق مع bulk-invoices-pdf view
                        'unitPrice'  => number_format($i->unit_price, 2),
                        'totalPrice' => number_format($i->total_price, 2),
                        'freeQuantity'  => 0,
                        'totalQuantity' => $i->quantity,
                    ]),
                ]);
            }

            return null;
        })->filter()->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shared.statistics.bulk-invoices-pdf', [
            'invoices'  => $invoices,
            'operation' => match($operation) {
                'invoices' => 'sales',
                'returns'  => 'sales_returns',
                default    => $operation,
            },
            'g'         => $g,
        ])
        ->setPaper('a4')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isFontSubsettingEnabled', true);

        $suffix = $limit ? ('-' . ($offset + 1) . '-' . ($offset + count($invoices))) : '';
        return $pdf->stream('customer-' . $operation . '-' . $request->from_date . $suffix . '.pdf');
    }

    // جلب البيانات كاملة بدون pagination للـ bulk
    private function getResultsForBulk(Request $request)
    {
        if (!$request->filled(['operation', 'from_date', 'to_date'])) return null;
        if (!$request->filled('customer_id') && !$request->filled('customer_name')) return null;
        if (in_array($request->operation, ['summary'])) return null;

        $query = match($request->operation) {
            'invoices' => CustomerInvoice::with(['customer', 'salesUser', 'items.product']),
            'payments' => CustomerPayment::with(['customer', 'salesUser']),
            'returns'  => CustomerReturn::with(['customer', 'salesUser', 'items.product', 'invoice']),
            default    => null
        };
        if (!$query) return null;

        if ($request->filled('customer_id') && $request->customer_id !== 'all') {
            $query->where('customer_id', $request->customer_id);
        } elseif ($request->filled('customer_name') && $request->customer_id !== 'all') {
            $query->whereHas('customer', fn($q) => $q->where('name', 'like', '%' . $request->customer_name . '%'));
        }
        if ($request->filled('sales_user_id')) $query->where('sales_user_id', $request->sales_user_id);
        if ($request->filled('status'))        $query->where('status', $request->status);

        $query->whereDate('created_at', '>=', $request->from_date)
              ->whereDate('created_at', '<=', $request->to_date);

        return ['operation' => $request->operation, 'data' => $query->latest()->get()];
    }
}
