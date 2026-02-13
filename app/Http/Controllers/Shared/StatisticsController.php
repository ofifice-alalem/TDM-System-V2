<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\SalesInvoice;
use App\Models\StorePayment;
use App\Models\SalesReturn;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::where('is_active', true)->get();
        $results = null;

        if ($request->filled(['stat_type', 'store_id', 'operation', 'from_date', 'to_date'])) {
            $results = $this->getStatistics($request);
            
            if ($request->has('export')) {
                return $this->exportToExcel($results, $request);
            }
        }

        return view('shared.statistics.index', compact('stores', 'results'));
    }

    private function getStatistics($request)
    {
        $query = match($request->operation) {
            'sales' => SalesInvoice::with('marketer', 'store')
                ->where('store_id', $request->store_id),
            'payments' => StorePayment::with('marketer', 'store', 'keeper')
                ->where('store_id', $request->store_id),
            'returns' => SalesReturn::with('marketer', 'store')
                ->where('store_id', $request->store_id),
            default => null
        };

        if (!$query) return null;

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->whereDate('created_at', '>=', $request->from_date)
              ->whereDate('created_at', '<=', $request->to_date);

        $data = $query->latest()->get();

        return [
            'operation' => $request->operation,
            'data' => $data,
            'total' => $data->where('status', 'approved')->sum(fn($item) => match($request->operation) {
                'sales' => $item->total_amount,
                'payments' => $item->amount,
                'returns' => $item->total_amount,
                default => 0
            })
        ];
    }

    private function exportToExcel($results, $request)
    {
        $filename = 'statistics_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($results, $request) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // معلومات الفلتر
            $store = \App\Models\Store::find($request->store_id);
            $operationName = match($request->operation) {
                'sales' => 'فواتير البيع',
                'payments' => 'إيصالات القبض',
                'returns' => 'إرجاعات البضاعة',
                default => ''
            };
            $statusName = match($request->status) {
                'pending' => 'معلق',
                'approved' => 'موثق',
                'cancelled' => 'ملغي',
                'rejected' => 'مرفوض',
                default => 'الكل'
            };
            
            fputcsv($file, ['نوع الإحصاء', 'المتاجر']);
            fputcsv($file, ['اسم المتجر', $store->name ?? '']);
            fputcsv($file, ['العملية', $operationName]);
            fputcsv($file, ['من تاريخ', $request->from_date]);
            fputcsv($file, ['إلى تاريخ', $request->to_date]);
            fputcsv($file, ['الحالة', $statusName]);
            fputcsv($file, ['الإجمالي (الموثق فقط)', number_format($results['total'], 2) . ' دينار']);
            fputcsv($file, []);
            
            // عناوين الجدول
            fputcsv($file, ['رقم الفاتورة', 'المسوق', 'التاريخ', 'الحالة', 'المبلغ']);
            
            foreach ($results['data'] as $item) {
                $invoiceNumber = match($results['operation']) {
                    'sales' => $item->invoice_number,
                    'payments' => $item->payment_number,
                    'returns' => $item->return_number,
                    default => ''
                };
                
                $amount = match($results['operation']) {
                    'sales' => $item->total_amount,
                    'payments' => $item->amount,
                    'returns' => $item->total_amount,
                    default => 0
                };
                
                $status = match($item->status) {
                    'pending' => 'معلق',
                    'approved' => 'موثق',
                    'cancelled' => 'ملغي',
                    'rejected' => 'مرفوض',
                    default => $item->status
                };
                
                fputcsv($file, [
                    $invoiceNumber,
                    $item->marketer->full_name,
                    $item->created_at->format('Y-m-d'),
                    $status,
                    number_format($amount, 2)
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['الإجمالي (الموثق فقط)', '', '', '', number_format($results['total'], 2)]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
