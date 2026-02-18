<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Models\CustomerPayment;
use App\Models\CustomerReturn;
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
        $customers = Customer::where('is_active', true)->get();
        $results = null;

        if ($request->filled(['customer_id', 'operation', 'from_date', 'to_date'])) {
            $results = $this->getStatistics($request, $request->has('export'));
            
            if ($results && $request->has('export')) {
                return $this->exportToExcel($results, $request);
            }
        }

        return view('sales.statistics.index', compact('customers', 'results'));
    }

    private function getStatistics($request, $forExport = false)
    {
        $query = match($request->operation) {
            'invoices' => CustomerInvoice::with(['customer', 'salesUser', 'returns' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }])->where('customer_id', $request->customer_id),
            'payments' => CustomerPayment::with('customer', 'salesUser')->where('customer_id', $request->customer_id),
            'returns' => CustomerReturn::with('customer', 'salesUser')->where('customer_id', $request->customer_id),
            default => null
        };

        if (!$query) return null;

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->whereDate('created_at', '>=', $request->from_date)
              ->whereDate('created_at', '<=', $request->to_date);

        $data = $forExport ? $query->latest()->get() : $query->latest()->paginate(50);
        
        $total = match($request->operation) {
            'invoices' => CustomerInvoice::where('customer_id', $request->customer_id)
                ->where('status', $request->filled('status') ? $request->status : 'completed')
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date)
                ->sum('total_amount'),
            'payments' => CustomerPayment::where('customer_id', $request->customer_id)
                ->where('status', $request->filled('status') ? $request->status : 'completed')
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date)
                ->sum('amount'),
            'returns' => CustomerReturn::where('customer_id', $request->customer_id)
                ->where('status', $request->filled('status') ? $request->status : 'completed')
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date)
                ->sum('total_amount'),
            default => 0
        };

        return [
            'operation' => $request->operation,
            'data' => $data,
            'total' => $total
        ];
    }

    private function exportToExcel($results, $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        
        $customer = Customer::find($request->customer_id);
        
        $operationName = match($request->operation) {
            'invoices' => 'الفواتير',
            'payments' => 'المدفوعات',
            'returns' => 'المرتجعات',
            default => ''
        };
        
        $statusName = match($request->status) {
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => 'الكل'
        };
        
        $row = 1;
        
        $infoData = [
            ['اسم العميل', $customer->name ?? ''],
            ['رقم الهاتف', $customer->phone ?? ''],
            ['العملية', $operationName],
            ['من تاريخ', $request->from_date],
            ['إلى تاريخ', $request->to_date],
            ['الحالة', $statusName],
        ];
        
        if (!$request->filled('status')) {
            $infoData[] = ['ملاحظة', 'يتم احتساب العمليات المكتملة فقط'];
        }
        
        $infoData[] = ['الإجمالي', number_format($results['total'], 2) . ' دينار'];
        
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
        
        $headers = $results['operation'] == 'invoices' 
            ? ['الرقم', 'الموظف', 'التاريخ', 'الحالة', 'المبلغ', 'المرتجعات']
            : ['الرقم', 'الموظف', 'التاريخ', 'الحالة', 'المبلغ'];
        
        $sheet->fromArray($headers, null, 'A' . $row);
        
        $lastCol = $results['operation'] == 'invoices' ? 'F' : 'E';
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        
        foreach ($results['data'] as $item) {
            $number = match($results['operation']) {
                'invoices' => $item->invoice_number,
                'payments' => $item->payment_number,
                'returns' => $item->return_number,
                default => ''
            };
            
            $amount = match($results['operation']) {
                'invoices' => $item->total_amount,
                'payments' => $item->amount,
                'returns' => $item->total_amount,
                default => 0
            };
            
            if ($results['operation'] == 'payments') {
                $status = match($item->status) {
                    'completed' => 'مكتمل',
                    'cancelled' => 'ملغي',
                    default => $item->status
                };
                
                $statusColor = match($item->status) {
                    'completed' => '66BB6A',
                    'cancelled' => 'EF5350',
                    default => 'FFFFFF'
                };
            } else {
                $status = match($item->status) {
                    'completed' => 'مكتمل',
                    'cancelled' => 'ملغي',
                    default => $item->status
                };
                
                $statusColor = match($item->status) {
                    'completed' => '66BB6A',
                    'cancelled' => 'EF5350',
                    default => 'FFFFFF'
                };
            }
            
            $rowData = [
                $number,
                $item->salesUser->full_name ?? '',
                $item->created_at->format('Y-m-d'),
                $status,
                number_format($amount, 2)
            ];
            
            if ($results['operation'] == 'invoices') {
                $returns = $item->returns->pluck('return_number')->implode(', ');
                $rowData[] = $returns ?: '-';
            }
            
            $sheet->fromArray($rowData, null, 'A' . $row);
            
            $lastCol = $results['operation'] == 'invoices' ? 'F' : 'E';
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            
            $sheet->getStyle('D' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColor]],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;
        }
        
        foreach (range('A', $results['operation'] == 'invoices' ? 'F' : 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = date('y_m_d') . '_' . $customer->name . '_' . $operationName . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
