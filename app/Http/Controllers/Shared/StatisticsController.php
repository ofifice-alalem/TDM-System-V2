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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        
        $store = Store::find($request->store_id);
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
        
        $row = 1;
        
        // معلومات الفلتر - كارد
        $infoData = [
            ['نوع الإحصاء', 'المتاجر'],
            ['اسم المتجر', $store->name ?? ''],
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
        $headers = ['رقم الفاتورة', 'المسوق', 'التاريخ', 'الحالة', 'المبلغ'];
        $sheet->fromArray($headers, null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
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
            
            $statusColor = match($item->status) {
                'pending' => 'FFA726',
                'approved' => '66BB6A',
                'cancelled' => '9E9E9E',
                'rejected' => 'EF5350',
                default => 'FFFFFF'
            };
            
            $sheet->fromArray([
                $invoiceNumber,
                $item->marketer->full_name,
                $item->created_at->format('Y-m-d'),
                $status,
                number_format($amount, 2)
            ], null, 'A' . $row);
            
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            
            $sheet->getStyle('D' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColor]],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;
        }
        
        // ضبط عرض الأعمدة
        foreach (range('A', 'E') as $col) {
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
