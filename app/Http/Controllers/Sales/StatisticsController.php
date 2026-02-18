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

        $paymentMethodTotals = null;
        if ($request->operation === 'payments') {
            $status = $request->filled('status') ? $request->status : 'completed';
            $paymentMethodTotals = [
                'cash' => CustomerPayment::where('customer_id', $request->customer_id)
                    ->where('status', $status)
                    ->where('payment_method', 'cash')
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date)
                    ->sum('amount'),
                'transfer' => CustomerPayment::where('customer_id', $request->customer_id)
                    ->where('status', $status)
                    ->where('payment_method', 'transfer')
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date)
                    ->sum('amount'),
                'check' => CustomerPayment::where('customer_id', $request->customer_id)
                    ->where('status', $status)
                    ->where('payment_method', 'check')
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date)
                    ->sum('amount'),
            ];
        }

        return [
            'operation' => $request->operation,
            'data' => $data,
            'total' => $total,
            'paymentMethodTotals' => $paymentMethodTotals
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
        
        if ($results['operation'] == 'payments' && $results['paymentMethodTotals']) {
            $row++;
            
            $paymentMethodData = [
                ['إجمالي النقدي', number_format($results['paymentMethodTotals']['cash'], 2) . ' دينار'],
                ['إجمالي التحويل', number_format($results['paymentMethodTotals']['transfer'], 2) . ' دينار'],
                ['إجمالي الشيك', number_format($results['paymentMethodTotals']['check'], 2) . ' دينار'],
            ];
            
            foreach ($paymentMethodData as $pmData) {
                $sheet->setCellValue('A' . $row, $pmData[0]);
                $sheet->setCellValue('B' . $row, $pmData[1]);
                $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
                    'font' => ['bold' => true, 'size' => 12],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row++;
            }
        }
        
        $row++;
        
        $headers = $results['operation'] == 'invoices' 
            ? ['الرقم', 'الموظف', 'التاريخ', 'الحالة', 'المبلغ', 'المرتجعات']
            : ($results['operation'] == 'payments' 
                ? ['الرقم', 'الموظف', 'التاريخ', 'الحالة', 'طريقة الدفع', 'المبلغ']
                : ['الرقم', 'الموظف', 'التاريخ', 'الحالة', 'المبلغ']);
        
        $sheet->fromArray($headers, null, 'A' . $row);
        
        $lastCol = $results['operation'] == 'invoices' ? 'F' : ($results['operation'] == 'payments' ? 'F' : 'E');
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
            ];
            
            if ($results['operation'] == 'payments') {
                $paymentMethod = match($item->payment_method) {
                    'cash' => 'نقدي',
                    'transfer' => 'تحويل',
                    'check' => 'شيك',
                    default => $item->payment_method
                };
                $rowData[] = $paymentMethod;
            }
            
            $rowData[] = number_format($amount, 2);
            
            if ($results['operation'] == 'invoices') {
                $returns = $item->returns->pluck('return_number')->implode(', ');
                $rowData[] = $returns ?: '-';
            }
            
            $sheet->fromArray($rowData, null, 'A' . $row);
            
            $lastCol = $results['operation'] == 'invoices' ? 'F' : ($results['operation'] == 'payments' ? 'F' : 'E');
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
        
        if ($results['operation'] == 'payments' && $results['paymentMethodTotals']) {
            $row++;
            $sheet->setCellValue('A' . $row, 'ملاحظات:');
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
            ]);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'إجمالي النقدي:');
            $sheet->setCellValue('B' . $row, number_format($results['paymentMethodTotals']['cash'], 2) . ' دينار');
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'إجمالي التحويل:');
            $sheet->setCellValue('B' . $row, number_format($results['paymentMethodTotals']['transfer'], 2) . ' دينار');
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'إجمالي الشيك:');
            $sheet->setCellValue('B' . $row, number_format($results['paymentMethodTotals']['check'], 2) . ' دينار');
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
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
