<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\SalesInvoice;
use App\Models\StorePayment;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::where('is_active', true)->get();
        $marketers = \App\Models\User::where('role_id', 3)->where('is_active', true)->get();
        $results = null;

        if ($request->filled(['stat_type', 'from_date', 'to_date'])) {
            if ($request->stat_type == 'stores' && $request->filled(['store_id', 'operation'])) {
                $results = $this->getStatistics($request);
            } elseif ($request->stat_type == 'marketers' && $request->filled(['marketer_id', 'operation'])) {
                $results = $this->getMarketerStatistics($request);
            }
            
            if ($results && $request->has('export')) {
                return $this->exportToExcel($results, $request);
            }
        }

        return view('shared.statistics.index', compact('stores', 'marketers', 'results'));
    }

    public function getMarketerStores($marketerId)
    {
        $stores = SalesInvoice::where('marketer_id', $marketerId)
            ->with('store:id,name')
            ->select('store_id')
            ->distinct()
            ->get()
            ->pluck('store')
            ->filter()
            ->unique('id')
            ->values();
        
        return response()->json($stores);
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

    private function getMarketerStatistics($request)
    {
        $query = match($request->operation) {
            'requests' => \App\Models\MarketerRequest::with('marketer', 'items.product')
                ->where('marketer_id', $request->marketer_id),
            'returns' => \App\Models\MarketerReturnRequest::with('marketer', 'items.product')
                ->where('marketer_id', $request->marketer_id),
            'sales' => SalesInvoice::with('marketer', 'store')
                ->where('marketer_id', $request->marketer_id),
            'payments' => StorePayment::with('marketer', 'store', 'keeper')
                ->where('marketer_id', $request->marketer_id),
            'withdrawals' => \App\Models\MarketerWithdrawalRequest::with('marketer')
                ->where('marketer_id', $request->marketer_id),
            default => null
        };

        if (!$query) return null;

        if ($request->filled('marketer_store_id') && in_array($request->operation, ['sales', 'payments'])) {
            $query->where('store_id', $request->marketer_store_id);
        }

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
                'withdrawals' => $item->requested_amount,
                default => 0
            })
        ];
    }

    private function exportToExcel($results, $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        
        $entity = null;
        if ($request->stat_type == 'stores') {
            $entity = Store::find($request->store_id);
            $entityLabel = 'اسم المتجر';
        } else {
            $entity = \App\Models\User::find($request->marketer_id);
            $entityLabel = 'اسم المسوق';
        }
        
        $operationName = match($request->operation) {
            'sales' => 'فواتير البيع',
            'payments' => 'إيصالات القبض',
            'returns' => 'إرجاعات البضاعة',
            'requests' => 'طلبات البضاعة',
            'withdrawals' => 'طلبات سحب الأرباح',
            default => ''
        };
        $statusName = match($request->status) {
            'pending' => 'معلق',
            'approved' => 'موافق عليه',
            'documented' => 'موثق',
            'cancelled' => 'ملغي',
            'rejected' => 'مرفوض',
            default => 'الكل'
        };
        
        $row = 1;
        
        // معلومات الفلتر - كارد
        $infoData = [
            ['نوع الإحصاء', $request->stat_type == 'stores' ? 'المتاجر' : 'المسوقين'],
            [$entityLabel, $entity->name ?? $entity->full_name ?? ''],
            ['العملية', $operationName],
            ['من تاريخ', $request->from_date],
            ['إلى تاريخ', $request->to_date],
            ['الحالة', $statusName],
            ['الإجمالي (الموثق فقط)', number_format($results['total'], 2) . ' دينار'],
        ];
        
        foreach ($infoData as $info) {
            $sheet->setCellValue('A' . $row, $info[0]);
            $sheet->setCellValue('B' . $row, $info[1]);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                'font' => ['bold' => true, 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
        }
        
        $row++;
        
        // عناوين الجدول
        if ($request->stat_type == 'stores') {
            $headers = ['رقم الفاتورة', 'المسوق', 'التاريخ', 'الحالة', 'المبلغ'];
        } elseif ($request->stat_type == 'marketers' && in_array($request->operation, ['sales', 'payments'])) {
            $headers = ['رقم الفاتورة', 'المتجر', 'التاريخ', 'الحالة', 'المبلغ'];
        } else {
            $headers = ['رقم الفاتورة', 'التاريخ', 'الحالة', 'المبلغ'];
        }
        
        $sheet->fromArray($headers, null, 'A' . $row);
        $lastCol = ($request->stat_type == 'stores' || ($request->stat_type == 'marketers' && in_array($request->operation, ['sales', 'payments']))) ? 'E' : 'D';
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        
        // البيانات
        foreach ($results['data'] as $item) {
            $invoiceNumber = match($results['operation']) {
                'sales' => $item->invoice_number,
                'payments' => $item->payment_number,
                'returns' => $item->return_number ?? null,
                'requests' => $item->invoice_number,
                'withdrawals' => 'WD-' . $item->id,
                default => ''
            };
            
            $amount = match($results['operation']) {
                'sales' => $item->total_amount,
                'payments' => $item->amount,
                'returns' => $item->total_amount ?? 0,
                'withdrawals' => $item->requested_amount,
                default => 0
            };
            
            $status = match($item->status) {
                'pending' => 'معلق',
                'approved' => 'موافق عليه',
                'documented' => 'موثق',
                'cancelled' => 'ملغي',
                'rejected' => 'مرفوض',
                default => $item->status
            };
            
            $statusColor = match($item->status) {
                'pending' => 'FFA726',
                'approved' => '42A5F5',
                'documented' => '66BB6A',
                'cancelled' => '9E9E9E',
                'rejected' => 'EF5350',
                default => 'FFFFFF'
            };
            
            if ($request->stat_type == 'stores') {
                $rowData = [
                    $invoiceNumber,
                    $item->marketer->full_name ?? '',
                    $item->created_at->format('Y-m-d'),
                    $status,
                    number_format($amount, 2)
                ];
                $statusCol = 'D';
            } elseif ($request->stat_type == 'marketers' && in_array($request->operation, ['sales', 'payments'])) {
                $rowData = [
                    $invoiceNumber,
                    $item->store->name ?? '',
                    $item->created_at->format('Y-m-d'),
                    $status,
                    number_format($amount, 2)
                ];
                $statusCol = 'D';
            } else {
                $rowData = [
                    $invoiceNumber,
                    $item->created_at->format('Y-m-d'),
                    $status,
                    number_format($amount, 2)
                ];
                $statusCol = 'C';
            }
            
            $sheet->fromArray($rowData, null, 'A' . $row);
            
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            
            $sheet->getStyle($statusCol . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColor]],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;
        }
        
        // ضبط عرض الأعمدة
        $maxCol = ($request->stat_type == 'stores' || ($request->stat_type == 'marketers' && in_array($request->operation, ['sales', 'payments']))) ? 'E' : 'D';
        foreach (range('A', $maxCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'statistics_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
