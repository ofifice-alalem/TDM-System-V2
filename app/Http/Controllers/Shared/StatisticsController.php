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
            if ($request->stat_type == 'stores' && $request->filled('operation')) {
                $results = $this->getStatistics($request, $request->has('export'));
            } elseif ($request->stat_type == 'marketers' && $request->filled(['marketer_id', 'operation'])) {
                $results = $this->getMarketerStatistics($request, $request->has('export'));
            }
            
            if ($results && $request->has('export')) {
                return $this->exportToExcel($results, $request);
            }
        }

        return view('shared.statistics.index', compact('stores', 'marketers', 'results'));
    }

    public function getMarketerStores($marketerId)
    {
        // Get stores from sales invoices
        $salesStores = SalesInvoice::where('marketer_id', $marketerId)
            ->join('stores', 'sales_invoices.store_id', '=', 'stores.id')
            ->select('stores.id', 'stores.name')
            ->distinct()
            ->get();
        
        // Get stores from payments
        $paymentStores = StorePayment::where('marketer_id', $marketerId)
            ->join('stores', 'store_payments.store_id', '=', 'stores.id')
            ->select('stores.id', 'stores.name')
            ->distinct()
            ->get();
        
        // Merge and remove duplicates
        $stores = $salesStores->merge($paymentStores)->unique('id')->values();
        
        return response()->json($stores);
    }

    private function getStatistics($request, $forExport = false)
    {
        // Handle summary operation
        if ($request->operation == 'summary') {
            return $this->getStoreSummary($request);
        }
        
        $query = match($request->operation) {
            'sales' => SalesInvoice::with('marketer', 'store'),
            'payments' => StorePayment::with('marketer', 'store', 'keeper'),
            'returns' => SalesReturn::with('marketer', 'store'),
            default => null
        };

        if (!$query) return null;

        // Filter by store_id only if not "all"
        if ($request->store_id && $request->store_id !== 'all') {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->whereDate('created_at', '>=', $request->from_date)
              ->whereDate('created_at', '<=', $request->to_date);

        $data = $forExport ? $query->latest()->get() : $query->latest()->paginate(50);
        
        // Calculate totals by status
        $statusTotals = [
            'pending' => 0,
            'approved' => 0,
            'cancelled' => 0,
            'rejected' => 0,
            'total' => 0
        ];
        
        if ($request->operation == 'payments') {
            foreach (['pending', 'approved', 'cancelled', 'rejected'] as $status) {
                $statusQuery = StorePayment::where('status', $status)
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
                
                if ($request->store_id && $request->store_id !== 'all') {
                    $statusQuery->where('store_id', $request->store_id);
                }
                
                $statusTotals[$status] = $statusQuery->sum('amount');
            }
            
            // Calculate payment method totals (always calculate regardless of status filter)
            $paymentMethodTotals = [
                'cash' => 0,
                'transfer' => 0,
                'certified_check' => 0,
                'total' => 0
            ];
            
            foreach (['cash', 'transfer', 'certified_check'] as $method) {
                $methodQuery = StorePayment::where('payment_method', $method)
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
                
                if ($request->store_id && $request->store_id !== 'all') {
                    $methodQuery->where('store_id', $request->store_id);
                }
                
                if ($request->filled('status')) {
                    $methodQuery->where('status', $request->status);
                }
                
                $paymentMethodTotals[$method] = $methodQuery->sum('amount');
            }
            
            $paymentMethodTotals['total'] = array_sum([$paymentMethodTotals['cash'], $paymentMethodTotals['transfer'], $paymentMethodTotals['certified_check']]);
        } else {
            $paymentMethodTotals = null;
        }
        
        if ($request->operation == 'sales') {
            foreach (['pending', 'approved', 'cancelled', 'rejected'] as $status) {
                $statusQuery = SalesInvoice::where('status', $status)
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
                
                if ($request->store_id && $request->store_id !== 'all') {
                    $statusQuery->where('store_id', $request->store_id);
                }
                
                $statusTotals[$status] = $statusQuery->sum('total_amount');
            }
        } elseif ($request->operation == 'returns') {
            foreach (['pending', 'approved', 'cancelled', 'rejected'] as $status) {
                $statusQuery = SalesReturn::where('status', $status)
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
                
                if ($request->store_id && $request->store_id !== 'all') {
                    $statusQuery->where('store_id', $request->store_id);
                }
                
                $statusTotals[$status] = $statusQuery->sum('total_amount');
            }
        }
        
        $statusTotals['total'] = array_sum([$statusTotals['pending'], $statusTotals['approved'], $statusTotals['cancelled'], $statusTotals['rejected']]);
        
        // Calculate total based on selected status or approved by default
        if ($request->filled('status')) {
            $total = $statusTotals[$request->status] ?? 0;
        } else {
            $total = $statusTotals['approved'];
        }

        return [
            'operation' => $request->operation,
            'data' => $data,
            'total' => $total,
            'total_commission' => 0,
            'status_totals' => $statusTotals,
            'payment_method_totals' => $paymentMethodTotals ?? null
        ];
    }

    private function exportSummaryToExcel($results, $request, $spreadsheet, $sheet)
    {
        $entity = null;
        if ($request->store_id == 'all') {
            $entityLabel = 'اسم المتجر';
            $entityName = 'الكل';
        } else {
            $entity = Store::find($request->store_id);
            $entityLabel = 'اسم المتجر';
            $entityName = $entity->name ?? '';
        }
        
        $row = 1;
        
        // معلومات الفلتر
        $infoData = [
            ['نوع الإحصاء', 'المتاجر'],
            [$entityLabel, $entityName],
            ['العملية', 'الملخص المالي'],
            ['من تاريخ', $request->from_date],
            ['إلى تاريخ', $request->to_date],
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
        
        // الملخص الإجمالي
        $sheet->setCellValue('A' . $row, 'الملخص الإجمالي');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        
        $summaryData = [
            ['إجمالي المبيعات', number_format($results['total_sales'], 2) . ' دينار'],
            ['إجمالي المدفوعات', number_format($results['total_payments'], 2) . ' دينار'],
            ['إجمالي المرتجعات', number_format($results['total_returns'], 2) . ' دينار'],
            ['الدين', number_format($results['current_balance'], 2) . ' دينار'],
        ];
        
        foreach ($summaryData as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'font' => ['bold' => true],
            ]);
            $row++;
        }
        
        // تفاصيل المتاجر إذا كان "الكل"
        if (isset($results['stores_data']) && count($results['stores_data']) > 0) {
            $row++;
            $sheet->setCellValue('A' . $row, 'تفاصيل المتاجر');
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;
            
            $headers = ['المتجر', 'إجمالي المبيعات', 'إجمالي المدفوع', 'إجمالي المرتجعات', 'الدين'];
            $sheet->fromArray($headers, null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '66BB6A']],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;
            
            foreach ($results['stores_data'] as $storeData) {
                $rowData = [
                    $storeData['store_name'],
                    number_format($storeData['sales'], 2),
                    number_format($storeData['payments'], 2),
                    number_format($storeData['returns'], 2),
                    number_format($storeData['balance'], 2)
                ];
                $sheet->fromArray($rowData, null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row++;
            }
        }
        
        // ضبط عرض الأعمدة
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = date('Y-m-d') . '__الملخص_المالي__' . $entityName . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function getMarketerStatistics($request, $forExport = false)
    {
        $query = match($request->operation) {
            'requests' => \App\Models\MarketerRequest::with('marketer', 'items.product')
                ->where('marketer_id', $request->marketer_id),
            'returns' => \App\Models\MarketerReturnRequest::with('marketer', 'items.product')
                ->where('marketer_id', $request->marketer_id),
            'sales_returns' => SalesReturn::with('marketer', 'store')
                ->where('marketer_id', $request->marketer_id),
            'sales' => SalesInvoice::with('marketer', 'store')
                ->where('marketer_id', $request->marketer_id),
            'payments' => StorePayment::with('marketer', 'store', 'keeper', 'commission')
                ->where('marketer_id', $request->marketer_id),
            'withdrawals' => \App\Models\MarketerWithdrawalRequest::with('marketer')
                ->where('marketer_id', $request->marketer_id),
            default => null
        };

        if (!$query) return null;

        if ($request->filled('marketer_store_id') && in_array($request->operation, ['sales', 'payments', 'sales_returns'])) {
            $query->where('store_id', $request->marketer_store_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->whereDate('created_at', '>=', $request->from_date)
              ->whereDate('created_at', '<=', $request->to_date);

        $data = $forExport ? $query->latest()->get() : $query->latest()->paginate(50);
        
        // Calculate totals based on operation
        $total = 0;
        $totalCommission = 0;
        $statusTotals = [
            'pending' => 0,
            'approved' => 0,
            'cancelled' => 0,
            'rejected' => 0,
            'total' => 0
        ];
        
        if ($request->operation == 'payments') {
            $totalQuery = StorePayment::where('marketer_id', $request->marketer_id)
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date);
            
            if ($request->filled('marketer_store_id')) {
                $totalQuery->where('store_id', $request->marketer_store_id);
            }
            
            if ($request->filled('status')) {
                $totalQuery->where('status', $request->status);
            } else {
                $totalQuery->where('status', 'approved');
            }
            
            $total = $totalQuery->sum('amount');
            
            $totalCommission = \App\Models\MarketerCommission::whereIn('payment_id', 
                $totalQuery->pluck('id')
            )->sum('commission_amount');
            
            // Calculate status totals
            foreach (['pending', 'approved', 'cancelled', 'rejected'] as $status) {
                $statusQuery = StorePayment::where('marketer_id', $request->marketer_id)
                    ->where('status', $status)
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
                
                if ($request->filled('marketer_store_id')) {
                    $statusQuery->where('store_id', $request->marketer_store_id);
                }
                
                $statusTotals[$status] = $statusQuery->sum('amount');
            }
            $statusTotals['total'] = array_sum([$statusTotals['pending'], $statusTotals['approved'], $statusTotals['cancelled'], $statusTotals['rejected']]);
        } elseif ($request->operation == 'sales') {
            $totalQuery = SalesInvoice::where('marketer_id', $request->marketer_id)
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date);
            
            if ($request->filled('marketer_store_id')) {
                $totalQuery->where('store_id', $request->marketer_store_id);
            }
            
            if ($request->filled('status')) {
                $totalQuery->where('status', $request->status);
            } else {
                $totalQuery->where('status', 'approved');
            }
            
            $total = $totalQuery->sum('total_amount');
            
            // Calculate status totals
            foreach (['pending', 'approved', 'cancelled', 'rejected'] as $status) {
                $statusQuery = SalesInvoice::where('marketer_id', $request->marketer_id)
                    ->where('status', $status)
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
                
                if ($request->filled('marketer_store_id')) {
                    $statusQuery->where('store_id', $request->marketer_store_id);
                }
                
                $statusTotals[$status] = $statusQuery->sum('total_amount');
            }
            $statusTotals['total'] = array_sum([$statusTotals['pending'], $statusTotals['approved'], $statusTotals['cancelled'], $statusTotals['rejected']]);
        } elseif ($request->operation == 'returns') {
            $totalQuery = \App\Models\MarketerReturnRequest::where('marketer_id', $request->marketer_id)
                ->where('status', 'approved')
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date);
            
            if ($request->filled('status')) {
                $totalQuery->where('status', $request->status);
            }
            
            $total = $totalQuery->sum('total_amount');
        } elseif ($request->operation == 'sales_returns') {
            $totalQuery = SalesReturn::where('marketer_id', $request->marketer_id)
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date);
            
            if ($request->filled('marketer_store_id')) {
                $totalQuery->where('store_id', $request->marketer_store_id);
            }
            
            if ($request->filled('status')) {
                $totalQuery->where('status', $request->status);
            } else {
                $totalQuery->where('status', 'approved');
            }
            
            $total = $totalQuery->sum('total_amount');
            
            // Calculate status totals
            foreach (['pending', 'approved', 'cancelled', 'rejected'] as $status) {
                $statusQuery = SalesReturn::where('marketer_id', $request->marketer_id)
                    ->where('status', $status)
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
                
                if ($request->filled('marketer_store_id')) {
                    $statusQuery->where('store_id', $request->marketer_store_id);
                }
                
                $statusTotals[$status] = $statusQuery->sum('total_amount');
            }
            $statusTotals['total'] = array_sum([$statusTotals['pending'], $statusTotals['approved'], $statusTotals['cancelled'], $statusTotals['rejected']]);
        } elseif ($request->operation == 'withdrawals') {
            $totalQuery = \App\Models\MarketerWithdrawalRequest::where('marketer_id', $request->marketer_id)
                ->where('status', 'approved')
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date);
            
            if ($request->filled('status')) {
                $totalQuery->where('status', $request->status);
            }
            
            $total = $totalQuery->sum('requested_amount');
        }

        return [
            'operation' => $request->operation,
            'data' => $data,
            'total' => $total,
            'total_commission' => $totalCommission,
            'status_totals' => $statusTotals
        ];
    }

    private function exportToExcel($results, $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        
        // Handle summary export
        if (isset($results['is_summary']) && $results['is_summary']) {
            return $this->exportSummaryToExcel($results, $request, $spreadsheet, $sheet);
        }
        
        $entity = null;
        if ($request->stat_type == 'stores') {
            if ($request->store_id == 'all') {
                $entityLabel = 'اسم المتجر';
                $entityName = 'الكل';
            } else {
                $entity = Store::find($request->store_id);
                $entityLabel = 'اسم المتجر';
                $entityName = $entity->name ?? '';
            }
        } else {
            $entity = \App\Models\User::find($request->marketer_id);
            $entityLabel = 'اسم المسوق';
            $entityName = $entity->full_name ?? '';
        }
        
        $operationName = match($request->operation) {
            'sales' => 'فواتير البيع',
            'payments' => 'إيصالات القبض',
            'returns' => 'إرجاعات البضاعة',
            'sales_returns' => 'إرجاعات المتاجر',
            'requests' => 'طلبات البضاعة',
            'withdrawals' => 'طلبات سحب الأرباح',
            'summary' => 'الملخص المالي',
            default => ''
        };
        $statusName = match($request->status) {
            'pending' => 'معلق',
            'approved' => 'موثق',
            'documented' => 'موثق',
            'cancelled' => 'ملغي',
            'rejected' => 'مرفوض',
            default => 'الكل'
        };
        
        $row = 1;
        
        // معلومات الفلتر - كارد
        $infoData = [
            ['نوع الإحصاء', $request->stat_type == 'stores' ? 'المتاجر' : 'المسوقين'],
            [$entityLabel, $entityName],
            ['العملية', $operationName],
        ];
        
        // Add store filter for marketers if applicable
        if ($request->stat_type == 'marketers' && $request->filled('marketer_store_id') && in_array($request->operation, ['sales', 'payments'])) {
            $selectedStore = Store::find($request->marketer_store_id);
            $infoData[] = ['المتجر', $selectedStore->name ?? ''];
        }
        
        $infoData = array_merge($infoData, [
            ['من تاريخ', $request->from_date],
            ['إلى تاريخ', $request->to_date],
            ['الحالة', $statusName],
        ]);
        
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
        
        if ($request->stat_type == 'stores' && isset($results['status_totals']) && !$request->filled('status')) {
            $row++;
            $sheet->setCellValue('A' . $row, 'الإجماليات حسب الحالة');
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
                'font' => ['bold' => true, 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;
            
            $statusHeaders = ['معلق', 'ملغي', 'مرفوض', 'موثق', 'الكلي'];
            $sheet->fromArray($statusHeaders, null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFE0B2']],
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;
            
            $statusValues = [
                number_format($results['status_totals']['pending'], 2),
                number_format($results['status_totals']['cancelled'], 2),
                number_format($results['status_totals']['rejected'], 2),
                number_format($results['status_totals']['approved'], 2),
                number_format($results['status_totals']['total'], 2)
            ];
            $sheet->fromArray($statusValues, null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;
            
            // Add payment method totals if payments operation
            if ($request->operation == 'payments' && isset($results['payment_method_totals'])) {
                $row++;
                $sheet->setCellValue('A' . $row, 'الإجماليات حسب طريقة الدفع');
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E1F5FE']],
                    'font' => ['bold' => true, 'size' => 12],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
                
                $methodHeaders = ['كاش', 'حوالة', 'شيك مصدق', 'الإجمالي'];
                $sheet->fromArray($methodHeaders, null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B3E5FC']],
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
                
                $methodValues = [
                    number_format($results['payment_method_totals']['cash'], 2),
                    number_format($results['payment_method_totals']['transfer'], 2),
                    number_format($results['payment_method_totals']['certified_check'], 2),
                    number_format($results['payment_method_totals']['total'], 2)
                ];
                $sheet->fromArray($methodValues, null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
            }
        } elseif ($request->stat_type == 'marketers') {
            $row++;
            
            // Show status totals if no status filter
            if (!$request->filled('status') && isset($results['status_totals'])) {
                $sheet->setCellValue('A' . $row, 'الإجماليات حسب الحالة');
                $sheet->mergeCells('A' . $row . ':E' . $row);
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
                    'font' => ['bold' => true, 'size' => 12],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
                
                $statusHeaders = ['معلق', 'ملغي', 'مرفوض', 'موثق', 'الكلي'];
                $sheet->fromArray($statusHeaders, null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFE0B2']],
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
                
                $statusValues = [
                    number_format($results['status_totals']['pending'], 2),
                    number_format($results['status_totals']['cancelled'], 2),
                    number_format($results['status_totals']['rejected'], 2),
                    number_format($results['status_totals']['approved'], 2),
                    number_format($results['status_totals']['total'], 2)
                ];
                $sheet->fromArray($statusValues, null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
                $row++;
            }
            
            $sheet->setCellValue('A' . $row, 'الإجمالي (الموثق فقط)');
            $sheet->setCellValue('B' . $row, number_format($results['total'], 2) . ' دينار');
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                'font' => ['bold' => true, 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
        }
        
        if ($request->stat_type == 'stores' && $request->filled('status')) {
            $row++;
            $sheet->setCellValue('A' . $row, 'الإجمالي');
            $sheet->setCellValue('B' . $row, number_format($results['total'], 2) . ' دينار');
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                'font' => ['bold' => true, 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
            
            // Add payment method totals if payments operation
            if ($request->operation == 'payments' && isset($results['payment_method_totals'])) {
                $row++;
                $sheet->setCellValue('A' . $row, 'الإجماليات حسب طريقة الدفع');
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E1F5FE']],
                    'font' => ['bold' => true, 'size' => 12],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
                
                $methodHeaders = ['كاش', 'حوالة', 'شيك مصدق', 'الإجمالي'];
                $sheet->fromArray($methodHeaders, null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B3E5FC']],
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
                
                $methodValues = [
                    number_format($results['payment_method_totals']['cash'], 2),
                    number_format($results['payment_method_totals']['transfer'], 2),
                    number_format($results['payment_method_totals']['certified_check'], 2),
                    number_format($results['payment_method_totals']['total'], 2)
                ];
                $sheet->fromArray($methodValues, null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
            }
        }
        
        $row++;
        
        // عناوين الجدول
        if ($request->stat_type == 'stores') {
            if ($request->operation == 'payments') {
                if ($request->store_id == 'all') {
                    $headers = ['رقم الفاتورة', 'المتجر', 'المسوق', 'طريقة الدفع', 'التاريخ', 'الحالة', 'المبلغ'];
                    $lastCol = 'G';
                } else {
                    $headers = ['رقم الفاتورة', 'المسوق', 'طريقة الدفع', 'التاريخ', 'الحالة', 'المبلغ'];
                    $lastCol = 'F';
                }
            } else {
                if ($request->store_id == 'all') {
                    $headers = ['رقم الفاتورة', 'المتجر', 'المسوق', 'التاريخ', 'الحالة', 'المبلغ'];
                    $lastCol = 'F';
                } else {
                    $headers = ['رقم الفاتورة', 'المسوق', 'التاريخ', 'الحالة', 'المبلغ'];
                    $lastCol = 'E';
                }
            }
        } elseif ($request->stat_type == 'marketers' && in_array($request->operation, ['sales', 'payments'])) {
            if ($request->operation == 'payments') {
                $headers = ['رقم الفاتورة', 'المتجر', 'نسبة العمولة', 'القيمة المستحقة', 'طريقة الدفع', 'التاريخ', 'الحالة', 'المبلغ'];
                $lastCol = 'H';
            } else {
                $headers = ['رقم الفاتورة', 'المتجر', 'التاريخ', 'الحالة', 'المبلغ'];
                $lastCol = 'E';
            }
        } else {
            $headers = ['رقم الفاتورة', 'التاريخ', 'الحالة', 'المبلغ'];
            $lastCol = 'D';
        }
        
        $sheet->fromArray($headers, null, 'A' . $row);
        
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
                'sales_returns' => $item->return_number ?? null,
                'requests' => $item->invoice_number,
                'withdrawals' => 'WD-' . $item->id,
                default => ''
            };
            
            $amount = match($results['operation']) {
                'sales' => $item->total_amount,
                'payments' => $item->amount,
                'returns' => $item->total_amount ?? 0,
                'sales_returns' => $item->total_amount ?? 0,
                'withdrawals' => $item->requested_amount,
                default => 0
            };
            
            $status = match($item->status) {
                'pending' => 'معلق',
                'approved' => 'موثق',
                'documented' => 'موثق',
                'cancelled' => 'ملغي',
                'rejected' => 'مرفوض',
                default => $item->status
            };
            
            $paymentMethod = match($item->payment_method ?? null) {
                'cash' => 'كاش',
                'transfer' => 'حوالة',
                'certified_check' => 'شيك مصدق',
                default => '-'
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
                if ($request->operation == 'payments') {
                    if ($request->store_id == 'all') {
                        $rowData = [
                            $invoiceNumber,
                            $item->store->name ?? '',
                            $item->marketer->full_name ?? '',
                            $paymentMethod,
                            $item->created_at->format('Y-m-d'),
                            $status,
                            number_format($amount, 2)
                        ];
                        $statusCol = 'F';
                    } else {
                        $rowData = [
                            $invoiceNumber,
                            $item->marketer->full_name ?? '',
                            $paymentMethod,
                            $item->created_at->format('Y-m-d'),
                            $status,
                            number_format($amount, 2)
                        ];
                        $statusCol = 'E';
                    }
                } else {
                    if ($request->store_id == 'all') {
                        $rowData = [
                            $invoiceNumber,
                            $item->store->name ?? '',
                            $item->marketer->full_name ?? '',
                            $item->created_at->format('Y-m-d'),
                            $status,
                            number_format($amount, 2)
                        ];
                        $statusCol = 'E';
                    } else {
                        $rowData = [
                            $invoiceNumber,
                            $item->marketer->full_name ?? '',
                            $item->created_at->format('Y-m-d'),
                            $status,
                            number_format($amount, 2)
                        ];
                        $statusCol = 'D';
                    }
                }
            } elseif ($request->stat_type == 'marketers' && in_array($request->operation, ['sales', 'payments'])) {
                if ($request->operation == 'payments') {
                    $rowData = [
                        $invoiceNumber,
                        $item->store->name ?? '',
                        ($item->commission->commission_rate ?? '-') . '%',
                        number_format($item->commission->commission_amount ?? 0, 2),
                        $paymentMethod,
                        $item->created_at->format('Y-m-d'),
                        $status,
                        number_format($amount, 2)
                    ];
                    $statusCol = 'G';
                } else {
                    $rowData = [
                        $invoiceNumber,
                        $item->store->name ?? '',
                        $item->created_at->format('Y-m-d'),
                        $status,
                        number_format($amount, 2)
                    ];
                    $statusCol = 'D';
                }
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
        $maxCol = 'E';
        foreach (range('A', $maxCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = date('Y-m-d') . '__' . $operationName . '__' . $entityName . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function getStoreSummary($request)
    {
        $storeQuery = $request->store_id !== 'all' ? fn($q) => $q->where('store_id', $request->store_id) : fn($q) => $q;
        
        $totalSales = SalesInvoice::where('status', 'approved')
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->when($request->store_id !== 'all', $storeQuery)
            ->sum('total_amount');
        
        $totalPayments = StorePayment::where('status', 'approved')
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->when($request->store_id !== 'all', $storeQuery)
            ->sum('amount');
        
        $totalReturns = SalesReturn::where('status', 'approved')
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->when($request->store_id !== 'all', $storeQuery)
            ->sum('total_amount');
        
        $currentBalance = $totalSales - $totalPayments - $totalReturns;
        
        // Get stores data if "all" is selected
        $storesData = [];
        if ($request->store_id === 'all') {
            $stores = Store::where('is_active', true)->get();
            foreach ($stores as $store) {
                $sales = SalesInvoice::where('status', 'approved')
                    ->where('store_id', $store->id)
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date)
                    ->sum('total_amount');
                
                $payments = StorePayment::where('status', 'approved')
                    ->where('store_id', $store->id)
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date)
                    ->sum('amount');
                
                $returns = SalesReturn::where('status', 'approved')
                    ->where('store_id', $store->id)
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date)
                    ->sum('total_amount');
                
                $balance = $sales - $payments - $returns;
                
                $storesData[] = [
                    'store_name' => $store->name,
                    'sales' => $sales,
                    'payments' => $payments,
                    'returns' => $returns,
                    'balance' => $balance
                ];
            }
        }
        
        return [
            'is_summary' => true,
            'total_sales' => $totalSales,
            'total_payments' => $totalPayments,
            'total_returns' => $totalReturns,
            'current_balance' => $currentBalance,
            'stores_data' => $storesData
        ];
    }
}
