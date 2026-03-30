<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Models\CustomerPayment;
use App\Models\CustomerReturn;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StatisticsController extends Controller
{
    protected function viewPrefix(): string { return 'sales.statistics'; }

    public function index(Request $request)
    {
        $customers = Customer::where('is_active', true)->get();
        $salesUsers = User::where('role_id', 4)->where('is_active', true)->orderBy('full_name')->get();
        $results = null;

        if ($request->filled(['operation', 'from_date', 'to_date']) &&
            ($request->filled('customer_id') || $request->filled('customer_name'))) {
            $results = $this->getStatistics($request, $request->has('export'));
            
            if ($results && $request->has('export')) {
                return $this->exportToExcel($results, $request);
            }
        }

        return view($this->viewPrefix() . '.index', compact('customers', 'salesUsers', 'results'));
    }

    private function getStatistics($request, $forExport = false)
    {
        $query = match($request->operation) {
            'invoices' => CustomerInvoice::with(['customer', 'salesUser', 'returns' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }]),
            'payments' => CustomerPayment::with('customer', 'salesUser'),
            'returns'  => CustomerReturn::with('customer', 'salesUser'),
            'summary'  => CustomerInvoice::with(['customer', 'salesUser']),
            default    => null
        };

        if (!$query) return null;

        if ($request->filled('customer_id') && $request->customer_id !== 'all') {
            $query->where('customer_id', $request->customer_id);
        } elseif ($request->filled('customer_name') && $request->customer_id !== 'all') {
            $query->whereHas('customer', fn($q) => $q->where('name', 'like', '%' . $request->customer_name . '%'));
        }

        if ($request->filled('sales_user_id')) {
            $query->where('sales_user_id', $request->sales_user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->whereDate('created_at', '>=', $request->from_date)
              ->whereDate('created_at', '<=', $request->to_date);

        // للملخص المالي نعيد بيانات مجمّعة حسب العميل
        if ($request->operation === 'summary') {
            $allItems = $query->where('status', 'completed')->get();

            $customerIds = $allItems->pluck('customer_id')->unique();

            $payments = CustomerPayment::with('customer')
                ->whereIn('customer_id', $customerIds)
                ->where('status', 'completed')
                ->when($request->filled('sales_user_id'), fn($q) => $q->where('sales_user_id', $request->sales_user_id))
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date)
                ->get();

            $returns = CustomerReturn::with('customer')
                ->whereIn('customer_id', $customerIds)
                ->where('status', 'completed')
                ->when($request->filled('sales_user_id'), fn($q) => $q->where('sales_user_id', $request->sales_user_id))
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date)
                ->get();

            $summaryData = $customerIds->map(function($cid) use ($allItems, $payments, $returns) {
                $customer       = $allItems->firstWhere('customer_id', $cid)?->customer;
                $totalInvoices  = $allItems->where('customer_id', $cid)->sum('total_amount');
                $totalPayments  = $payments->where('customer_id', $cid)->sum('amount');
                $totalReturns   = $returns->where('customer_id', $cid)->sum('total_amount');
                return (object)[
                    'customer'       => $customer,
                    'total_invoices' => $totalInvoices,
                    'total_payments' => $totalPayments,
                    'total_returns'  => $totalReturns,
                    'total_debt'     => $totalInvoices - $totalPayments - $totalReturns,
                ];
            })->sortByDesc('total_debt')->values();

            return [
                'operation'          => 'summary',
                'data'               => $summaryData,
                'total'              => $summaryData->sum('total_invoices'),
                'grand_payments'     => $summaryData->sum('total_payments'),
                'grand_returns'      => $summaryData->sum('total_returns'),
                'grand_debt'         => $summaryData->sum('total_debt'),
                'paymentMethodTotals'=> null,
            ];
        }

        $data = $forExport ? $query->latest()->get() : $query->latest()->paginate(50);
        
        $totalQuery = match($request->operation) {
            'invoices' => CustomerInvoice::query(),
            'payments' => CustomerPayment::query(),
            'returns' => CustomerReturn::query(),
            default => null
        };
        
        if ($request->filled('customer_id') && $request->customer_id !== 'all') {
            $totalQuery->where('customer_id', $request->customer_id);
        } elseif ($request->filled('customer_name') && $request->customer_id !== 'all') {
            $totalQuery->whereHas('customer', fn($q) => $q->where('name', 'like', '%' . $request->customer_name . '%'));
        }

        if ($request->filled('sales_user_id')) {
            $totalQuery->where('sales_user_id', $request->sales_user_id);
        }
        
        $totalQuery->where('status', $request->filled('status') ? $request->status : 'completed')
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date);
        
        $total = match($request->operation) {
            'invoices' => $totalQuery->sum('total_amount'),
            'payments' => $totalQuery->sum('amount'),
            'returns' => $totalQuery->sum('total_amount'),
            default => 0
        };

        $paymentMethodTotals = null;
        if ($request->operation === 'payments') {
            $status = $request->filled('status') ? $request->status : 'completed';
            $pmQuery = CustomerPayment::where('status', $status)
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date);
            
            if ($request->filled('customer_id') && $request->customer_id !== 'all') {
                $pmQuery->where('customer_id', $request->customer_id);
            } elseif ($request->filled('customer_name') && $request->customer_id !== 'all') {
                $pmQuery->whereHas('customer', fn($q) => $q->where('name', 'like', '%' . $request->customer_name . '%'));
            }

            if ($request->filled('sales_user_id')) {
                $pmQuery->where('sales_user_id', $request->sales_user_id);
            }
            
            $paymentMethodTotals = [
                'cash' => (clone $pmQuery)->where('payment_method', 'cash')->sum('amount'),
                'transfer' => (clone $pmQuery)->where('payment_method', 'transfer')->sum('amount'),
                'check' => (clone $pmQuery)->where('payment_method', 'check')->sum('amount'),
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
        
        $customer = ($request->customer_id && $request->customer_id !== 'all') 
            ? Customer::find($request->customer_id) 
            : null;

        $customerLabel = $customer 
            ? $customer->name 
            : ($request->filled('customer_name') && $request->customer_id !== 'all' 
                ? $request->customer_name . ' (بحث)'
                : 'الكل');
        
        $operationName = match($request->operation) {
            'invoices' => 'الفواتير',
            'payments' => 'المدفوعات',
            'returns'  => 'المرتجعات',
            'summary'  => 'الملخص المالي',
            default    => ''
        };
        
        $row = 1;
        
        $infoData = [
            ['اسم العميل', $customerLabel],
        ];
        
        if ($customer) {
            $infoData[] = ['رقم الهاتف', $customer->phone ?? ''];
        }
        
        $infoData = array_merge($infoData, [
            ['العملية', $operationName],
            ['الموظف', $request->filled('sales_user_id') ? (User::find($request->sales_user_id)?->full_name ?? '') : 'الكل'],
            ['من تاريخ', $request->from_date],
            ['إلى تاريخ', $request->to_date],
        ]);
        
        if ($results['operation'] !== 'summary') {
            $statusName = match($request->status ?? '') {
                'completed' => 'مكتمل',
                'cancelled' => 'ملغي',
                default     => 'الكل'
            };
            $infoData[] = ['الحالة', $statusName];
            if (!$request->filled('status')) {
                $infoData[] = ['ملاحظة', 'يتم احتساب العمليات المكتملة فقط'];
            }
        }
        
        if ($results['operation'] !== 'summary') {
            $infoData[] = ['الإجمالي', number_format($results['total'], 2) . ' دينار'];
        }
        
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

        if ($results['operation'] == 'summary') {
            $grandDebtColor = $results['grand_debt'] > 0 ? 'FFCDD2' : ($results['grand_debt'] < 0 ? 'C8E6C9' : 'F5F5F5');
            $sheet->fromArray(['إجمالي الفواتير', 'إجمالي المدفوعات', 'إجمالي المرتجعات', 'الدين'], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8EAF6']],
                'font' => ['bold' => true, 'size' => 11],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;
            $sheet->fromArray([number_format($results['total'], 2), number_format($results['grand_payments'], 2), number_format($results['grand_returns'], 2), number_format($results['grand_debt'], 2)], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4FF']],
                'font' => ['bold' => true, 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle('D' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $grandDebtColor]],
            ]);
            $row += 2;
        }
        
        $headers = $results['operation'] == 'invoices' 
            ? ['الرقم', 'العميل', 'الموظف', 'التاريخ', 'الحالة', 'المبلغ', 'المرتجعات']
            : ($results['operation'] == 'payments' 
                ? ['الرقم', 'العميل', 'الموظف', 'التاريخ', 'الحالة', 'طريقة الدفع', 'المبلغ']
                : ($results['operation'] == 'summary'
                    ? ['العميل', 'إجمالي الفواتير', 'إجمالي المدفوعات', 'إجمالي المرتجعات', 'الدين الحالي']
                    : ['الرقم', 'العميل', 'الموظف', 'التاريخ', 'الحالة', 'المبلغ']));
        
        $sheet->fromArray($headers, null, 'A' . $row);
        
        $lastCol = $results['operation'] == 'invoices' ? 'G' : ($results['operation'] == 'payments' ? 'G' : ($results['operation'] == 'summary' ? 'E' : 'F'));
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        
        foreach ($results['data'] as $item) {
            if ($results['operation'] == 'summary') {
                $rowData = [
                    $item->customer->name ?? '',
                    number_format($item->total_invoices, 2),
                    number_format($item->total_payments, 2),
                    number_format($item->total_returns, 2),
                    number_format($item->total_debt, 2),
                ];
                $sheet->fromArray($rowData, null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $debtColor = $item->total_debt > 0 ? 'FFCDD2' : ($item->total_debt < 0 ? 'C8E6C9' : 'F5F5F5');
                $sheet->getStyle('E' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $debtColor]],
                    'font' => ['bold' => true],
                ]);
                $row++;
                continue;
            }

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
                $item->customer->name ?? '',
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
            
            $lastCol = $results['operation'] == 'invoices' ? 'G' : ($results['operation'] == 'payments' ? 'G' : 'F');
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            
            $sheet->getStyle('E' . $row)->applyFromArray([
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
        
        $lastAutoCol = match($results['operation']) {
            'invoices' => 'G',
            'payments' => 'G',
            'summary'  => 'E',
            default    => 'F',
        };
        foreach (range('A', $lastAutoCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filenameOp = match($results['operation']) {
            'invoices' => 'invoices',
            'payments' => 'payments',
            'returns'  => 'returns',
            'summary'  => 'summary',
            default    => 'export',
        };
        $filename = date('y_m_d') . '_' . $filenameOp . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function quickInvoices(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $customers = Customer::withCount(['invoices' => function($q) use ($fromDate, $toDate) {
            $q->where('status', 'completed')
              ->whereDate('created_at', '>=', $fromDate)
              ->whereDate('created_at', '<=', $toDate);
        }])
        ->withSum(['invoices as invoices_total' => function($q) use ($fromDate, $toDate) {
            $q->where('status', 'completed')
              ->whereDate('created_at', '>=', $fromDate)
              ->whereDate('created_at', '<=', $toDate);
        }], 'total_amount')
        ->having('invoices_count', '>', 0)
        ->orderBy('invoices_total', 'desc')
        ->get();

        $totalInvoices = CustomerInvoice::where('status', 'completed')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->count();
        $totalAmount = CustomerInvoice::where('status', 'completed')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->sum('total_amount');

        if ($request->has('export')) {
            return $this->exportQuickInvoices($customers, $totalInvoices, $totalAmount);
        }

        return view($this->viewPrefix() . '.quick-invoices', compact('customers', 'totalInvoices', 'totalAmount', 'fromDate', 'toDate'));
    }

    public function quickPayments(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $customers = Customer::with(['payments' => function($q) use ($fromDate, $toDate) {
            $q->where('status', 'completed')
              ->whereDate('created_at', '>=', $fromDate)
              ->whereDate('created_at', '<=', $toDate);
        }])
        ->whereHas('payments', function($q) use ($fromDate, $toDate) {
            $q->where('status', 'completed')
              ->whereDate('created_at', '>=', $fromDate)
              ->whereDate('created_at', '<=', $toDate);
        })
        ->get()
        ->map(function($customer) {
            $payments = $customer->payments;
            $customer->cash_total = $payments->where('payment_method', 'cash')->sum('amount');
            $customer->transfer_total = $payments->where('payment_method', 'transfer')->sum('amount');
            $customer->check_total = $payments->where('payment_method', 'check')->sum('amount');
            $customer->total_payments = $payments->sum('amount');
            return $customer;
        })
        ->sortByDesc('total_payments');

        $cashTotal = CustomerPayment::where('status', 'completed')
            ->where('payment_method', 'cash')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->sum('amount');
        $transferTotal = CustomerPayment::where('status', 'completed')
            ->where('payment_method', 'transfer')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->sum('amount');
        $checkTotal = CustomerPayment::where('status', 'completed')
            ->where('payment_method', 'check')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->sum('amount');
        $totalAmount = $cashTotal + $transferTotal + $checkTotal;

        if ($request->has('export')) {
            return $this->exportQuickPayments($customers, $cashTotal, $transferTotal, $checkTotal, $totalAmount);
        }

        return view($this->viewPrefix() . '.quick-payments', compact('customers', 'cashTotal', 'transferTotal', 'checkTotal', 'totalAmount', 'fromDate', 'toDate'));
    }

    public function quickReturns(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $customers = Customer::withCount(['returns' => function($q) use ($fromDate, $toDate) {
            $q->where('status', 'completed')
              ->whereDate('created_at', '>=', $fromDate)
              ->whereDate('created_at', '<=', $toDate);
        }])
        ->withSum(['returns as returns_total' => function($q) use ($fromDate, $toDate) {
            $q->where('status', 'completed')
              ->whereDate('created_at', '>=', $fromDate)
              ->whereDate('created_at', '<=', $toDate);
        }], 'total_amount')
        ->having('returns_count', '>', 0)
        ->orderBy('returns_total', 'desc')
        ->get();

        $totalReturns = CustomerReturn::where('status', 'completed')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->count();
        $totalAmount = CustomerReturn::where('status', 'completed')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->sum('total_amount');

        if ($request->has('export')) {
            return $this->exportQuickReturns($customers, $totalReturns, $totalAmount);
        }

        return view($this->viewPrefix() . '.quick-returns', compact('customers', 'totalReturns', 'totalAmount', 'fromDate', 'toDate'));
    }

    public function quickSummary(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $customers = Customer::with([
            'invoices' => function($q) use ($fromDate, $toDate) {
                $q->where('status', 'completed')
                  ->whereDate('created_at', '>=', $fromDate)
                  ->whereDate('created_at', '<=', $toDate);
            },
            'payments' => function($q) use ($fromDate, $toDate) {
                $q->where('status', 'completed')
                  ->whereDate('created_at', '>=', $fromDate)
                  ->whereDate('created_at', '<=', $toDate);
            },
            'returns' => function($q) use ($fromDate, $toDate) {
                $q->where('status', 'completed')
                  ->whereDate('created_at', '>=', $fromDate)
                  ->whereDate('created_at', '<=', $toDate);
            }
        ])
        ->get()
        ->map(function($customer) {
            $customer->total_invoices = $customer->invoices->sum('total_amount');
            $customer->total_payments = $customer->payments->sum('amount');
            $customer->total_returns = $customer->returns->sum('total_amount');
            $customer->total_debt = $customer->total_invoices - $customer->total_payments - $customer->total_returns;
            return $customer;
        })
        ->filter(function($customer) {
            return $customer->total_invoices > 0 || $customer->total_payments > 0 || $customer->total_returns > 0;
        })
        ->sortByDesc('total_debt');

        if ($request->has('export')) {
            return $this->exportQuickSummary($customers, $fromDate, $toDate);
        }

        return view($this->viewPrefix() . '.quick-summary', compact('customers', 'fromDate', 'toDate'));
    }

    private function exportQuickInvoices($customers, $totalInvoices, $totalAmount)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $row = 1;
        $sheet->setCellValue('A' . $row, 'من تاريخ');
        $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse(request('from_date'))->format('d/m/Y'));
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $sheet->setCellValue('A' . $row, 'إلى تاريخ');
        $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse(request('to_date'))->format('d/m/Y'));
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row += 2;

        $sheet->setCellValue('A' . $row, 'إجمالي عدد الفواتير');
        $sheet->setCellValue('B' . $row, number_format($totalInvoices));
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $sheet->setCellValue('A' . $row, 'إجمالي المبلغ');
        $sheet->setCellValue('B' . $row, number_format($totalAmount, 2) . ' دينار');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row += 2;

        $sheet->fromArray(['العميل', 'عدد الفواتير', 'إجمالي المبلغ'], null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        foreach ($customers as $customer) {
            $sheet->fromArray([
                $customer->name,
                number_format($customer->invoices_count),
                number_format($customer->invoices_total ?? 0, 2)
            ], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = date('Y_m_d') . '_الفواتير_المكتملة.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function exportQuickPayments($customers, $cashTotal, $transferTotal, $checkTotal, $totalAmount)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $row = 1;
        $sheet->setCellValue('A' . $row, 'من تاريخ');
        $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse(request('from_date'))->format('d/m/Y'));
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $sheet->setCellValue('A' . $row, 'إلى تاريخ');
        $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse(request('to_date'))->format('d/m/Y'));
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row += 2;

        $sheet->setCellValue('A' . $row, 'إجمالي النقدي');
        $sheet->setCellValue('B' . $row, number_format($cashTotal, 2) . ' دينار');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $sheet->setCellValue('A' . $row, 'إجمالي التحويل');
        $sheet->setCellValue('B' . $row, number_format($transferTotal, 2) . ' دينار');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $sheet->setCellValue('A' . $row, 'إجمالي الشيك');
        $sheet->setCellValue('B' . $row, number_format($checkTotal, 2) . ' دينار');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $sheet->setCellValue('A' . $row, 'الإجمالي');
        $sheet->setCellValue('B' . $row, number_format($totalAmount, 2) . ' دينار');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3E5F5']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row += 2;

        $sheet->fromArray(['العميل', 'نقدي', 'تحويل', 'شيك', 'الإجمالي'], null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        foreach ($customers as $customer) {
            $sheet->fromArray([
                $customer->name,
                number_format($customer->cash_total, 2),
                number_format($customer->transfer_total, 2),
                number_format($customer->check_total, 2),
                number_format($customer->total_payments, 2)
            ], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = date('Y_m_d') . '_المدفوعات_المكتملة.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function exportQuickReturns($customers, $totalReturns, $totalAmount)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $row = 1;
        $sheet->setCellValue('A' . $row, 'من تاريخ');
        $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse(request('from_date'))->format('d/m/Y'));
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $sheet->setCellValue('A' . $row, 'إلى تاريخ');
        $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse(request('to_date'))->format('d/m/Y'));
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row += 2;

        $sheet->setCellValue('A' . $row, 'إجمالي عدد المرتجعات');
        $sheet->setCellValue('B' . $row, number_format($totalReturns));
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $sheet->setCellValue('A' . $row, 'إجمالي المبلغ');
        $sheet->setCellValue('B' . $row, number_format($totalAmount, 2) . ' دينار');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFEBEE']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row += 2;

        $sheet->fromArray(['العميل', 'عدد المرتجعات', 'إجمالي المبلغ'], null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF9800']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        foreach ($customers as $customer) {
            $sheet->fromArray([
                $customer->name,
                number_format($customer->returns_count),
                number_format($customer->returns_total ?? 0, 2)
            ], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = date('Y_m_d') . '_المرتجعات_المكتملة.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function exportQuickSummary($customers, $fromDate, $toDate)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $row = 1;
        $sheet->setCellValue('A' . $row, 'من تاريخ');
        $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse($fromDate)->format('d/m/Y'));
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $sheet->setCellValue('A' . $row, 'إلى تاريخ');
        $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse($toDate)->format('d/m/Y'));
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sumInvoices = $customers->sum('total_invoices');
        $sumPayments = $customers->sum('total_payments');
        $sumReturns  = $customers->sum('total_returns');
        $sumDebt     = $customers->sum('total_debt');

        $summaryData = [
            ['إجمالي الفواتير', number_format($sumInvoices, 2) . ' دينار'],
            ['إجمالي المدفوعات', number_format($sumPayments, 2) . ' دينار'],
            ['إجمالي المرتجعات', number_format($sumReturns, 2) . ' دينار'],
            ['إجمالي الدين', number_format($sumDebt, 2) . ' دينار'],
        ];

        $summaryColors = ['E3F2FD', 'E8F5E9', 'FFF3E0', $sumDebt > 0 ? 'FFCDD2' : ($sumDebt < 0 ? 'C8E6C9' : 'F5F5F5')];

        foreach ($summaryData as $i => $summary) {
            $sheet->setCellValue('A' . $row, $summary[0]);
            $sheet->setCellValue('B' . $row, $summary[1]);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $summaryColors[$i]]],
                'font' => ['bold' => true, 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
        }

        $row += 2;

        $sheet->fromArray(['العميل', 'إجمالي الفواتير', 'إجمالي المدفوعات', 'إجمالي المرتجعات', 'الدين الحالي'], null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '9C27B0']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        foreach ($customers as $customer) {
            $sheet->fromArray([
                $customer->name,
                number_format($customer->total_invoices, 2),
                number_format($customer->total_payments, 2),
                number_format($customer->total_returns, 2),
                number_format($customer->total_debt, 2)
            ], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            
            $debtColor = $customer->total_debt > 0 ? 'FFCDD2' : ($customer->total_debt < 0 ? 'C8E6C9' : 'F5F5F5');
            $sheet->getStyle('E' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $debtColor]],
                'font' => ['bold' => true],
            ]);
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = date('Y_m_d') . '_الملخص_المالي.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
