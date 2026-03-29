<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\StatisticsController as SharedStatisticsController;
use App\Models\Store;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $request->merge([
            'stat_type'   => 'marketers',
            'marketer_id' => (string) auth()->id(),
        ]);

        $stores = Store::where('marketer_id', auth()->id())->where('is_active', true)->get();
        $results = null;

        if ($request->filled(['operation', 'from_date', 'to_date'])) {
            $shared = new SharedStatisticsController();

            if ($request->operation === 'summary') {
                $results = $this->getMarketerStoreSummary($request);
            } else {
                $results = $this->callProtected($shared, 'getMarketerStatistics', [$request]);
            }

            if ($results && $request->has('export')) {
                if (isset($results['is_store_summary'])) {
                    return $this->exportStoreSummaryToExcel($results, $request);
                }
                return $this->callProtected($shared, 'exportToExcel', [$results, $request]);
            }
        }

        return view('marketer.statistics.index', compact('stores', 'results'));
    }

    public function marketerStores()
    {
        $stores = Store::where('marketer_id', auth()->id())
            ->where('is_active', true)
            ->select('id', 'name')
            ->get();

        return response()->json($stores);
    }

    private function exportStoreSummaryToExcel($results, $request)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $row = 1;

        // معلومات الفلتر
        foreach ([
            ['نوع الإحصاء', 'ملخص المتاجر'],
            ['المسوق', auth()->user()->full_name],
            ['من تاريخ', $request->from_date],
            ['إلى تاريخ', $request->to_date],
        ] as $info) {
            $sheet->setCellValue('A'.$row, $info[0]);
            $sheet->setCellValue('B'.$row, $info[1]);
            $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray([
                'fill'    => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                'font'    => ['bold' => true, 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            $row++;
        }

        $row++;

        // الملخص الإجمالي
        foreach ([
            ['إجمالي المبيعات',  number_format($results['total_sales'],    2).' دينار'],
            ['إجمالي المدفوعات', number_format($results['total_payments'], 2).' دينار'],
            ['إجمالي المرتجعات', number_format($results['total_returns'],  2).' دينار'],
            ['إجمالي الديون',    number_format($results['total_debt'],     2).' دينار'],
        ] as $data) {
            $sheet->setCellValue('A'.$row, $data[0]);
            $sheet->setCellValue('B'.$row, $data[1]);
            $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                'font'    => ['bold' => true],
            ]);
            $row++;
        }

        // الإجماليات حسب الحالة
        $row++;
        $sheet->setCellValue('A'.$row, 'حالة المبيعات');
        $sheet->mergeCells('A'.$row.':C'.$row);
        $sheet->getStyle('A'.$row)->applyFromArray([
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font'      => ['bold' => true, 'size' => 12],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        $sheet->fromArray(['معلق', 'موثق', 'الكلي'], null, 'A'.$row);
        $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFE0B2']],
            'font'      => ['bold' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        $st = $results['status_totals'];
        $sheet->fromArray([
            number_format($st['pending'],  2),
            number_format($st['approved'], 2),
            number_format($st['total'],    2),
        ], null, 'A'.$row);
        $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
            'font'      => ['bold' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        // حالة المدفوعات
        $row++;
        $sheet->setCellValue('A'.$row, 'حالة المدفوعات');
        $sheet->mergeCells('A'.$row.':C'.$row);
        $sheet->getStyle('A'.$row)->applyFromArray([
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
            'font'      => ['bold' => true, 'size' => 12],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        $sheet->fromArray(['معلق', 'موثق', 'الكلي'], null, 'A'.$row);
        $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C8E6C9']],
            'font'      => ['bold' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        $pt = $results['payment_status_totals'];
        $sheet->fromArray([
            number_format($pt['pending'],  2),
            number_format($pt['approved'], 2),
            number_format($pt['total'],    2),
        ], null, 'A'.$row);
        $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
            'font'      => ['bold' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        // حالة المرتجعات
        $row++;
        $sheet->setCellValue('A'.$row, 'حالة المرتجعات');
        $sheet->mergeCells('A'.$row.':C'.$row);
        $sheet->getStyle('A'.$row)->applyFromArray([
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBE9E7']],
            'font'      => ['bold' => true, 'size' => 12],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        $sheet->fromArray(['معلق', 'موثق', 'الكلي'], null, 'A'.$row);
        $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFCCBC']],
            'font'      => ['bold' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        $rt = $results['return_status_totals'];
        $sheet->fromArray([
            number_format($rt['pending'],  2),
            number_format($rt['approved'], 2),
            number_format($rt['total'],    2),
        ], null, 'A'.$row);
        $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
            'font'      => ['bold' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        // تفاصيل المتاجر
        if (count($results['stores_data']) > 0) {
            $row++;
            $headers = ['المتجر', 'المبيعات', 'المدفوعات', 'المرتجعات', 'الدين', 'دائن / مدين'];
            $sheet->fromArray($headers, null, 'A'.$row);
            $sheet->getStyle('A'.$row.':F'.$row)->applyFromArray([
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F57C00']],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;

            foreach ($results['stores_data'] as $r) {
                $sheet->fromArray([
                    $r['store_name'],
                    number_format($r['sales'],    2),
                    number_format($r['payments'], 2),
                    number_format($r['returns'],  2),
                    number_format($r['balance'],  2),
                    $r['balance'] > 0 ? 'مدين' : ($r['balance'] < 0 ? 'دائن' : '--'),
                ], null, 'A'.$row);
                $sheet->getStyle('A'.$row.':F'.$row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                if ($r['balance'] < 0) {
                    $sheet->getStyle('F'.$row)->applyFromArray([
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    ]);
                }
                $row++;
            }
        }

        foreach (range('A','F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = date('Y-m-d').'__ملخص_المتاجر__'.auth()->user()->full_name.'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    private function getMarketerStoreSummary($request)
    {
        $marketerId = auth()->id();
        $marketerStoreIds = Store::where('marketer_id', $marketerId)->pluck('id');

        $salesQuery = \App\Models\SalesInvoice::where('status', 'approved')
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->where(function($q) use ($marketerId, $marketerStoreIds) {
                $q->where('marketer_id', $marketerId)
                  ->orWhere(function($q2) use ($marketerStoreIds) {
                      $q2->where('marketer_id', 0)
                         ->whereIn('store_id', $marketerStoreIds);
                  });
            });

        $paymentsQuery = \App\Models\StorePayment::where('marketer_id', $marketerId)
            ->where('status', 'approved')
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date);

        $returnsQuery = \App\Models\SalesReturn::where('marketer_id', $marketerId)
            ->where('status', 'approved')
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date);

        $totalSales    = (clone $salesQuery)->sum('total_amount');
        $totalPayments = (clone $paymentsQuery)->sum('amount');
        $totalReturns  = (clone $returnsQuery)->sum('total_amount');

        // pending
        $pendingSales = \App\Models\SalesInvoice::where('status', 'pending')
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->where(function($q) use ($marketerId, $marketerStoreIds) {
                $q->where('marketer_id', $marketerId)
                  ->orWhere(function($q2) use ($marketerStoreIds) {
                      $q2->where('marketer_id', 0)->whereIn('store_id', $marketerStoreIds);
                  });
            })->sum('total_amount');

        $pendingPayments = \App\Models\StorePayment::where('marketer_id', $marketerId)
            ->where('status', 'pending')
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->sum('amount');

        $pendingReturns = \App\Models\SalesReturn::where('marketer_id', $marketerId)
            ->where('status', 'pending')
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->sum('total_amount');

        $totalDebt = ($totalSales + $pendingSales) - ($totalPayments + $pendingPayments) - ($totalReturns + $pendingReturns);

        // status totals للمبيعات
        $salesByStatus = \App\Models\SalesInvoice::whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->where(function($q) use ($marketerId, $marketerStoreIds) {
                $q->where('marketer_id', $marketerId)
                  ->orWhere(function($q2) use ($marketerStoreIds) {
                      $q2->where('marketer_id', 0)->whereIn('store_id', $marketerStoreIds);
                  });
            })
            ->selectRaw('status, SUM(total_amount) as total')
            ->groupBy('status')->pluck('total', 'status');

        $statusTotals = [
            'pending'  => $salesByStatus['pending']  ?? 0,
            'approved' => $salesByStatus['approved'] ?? 0,
        ];
        $statusTotals['total'] = array_sum($statusTotals);

        // status totals للمدفوعات
        $paymentsByStatus = \App\Models\StorePayment::where('marketer_id', $marketerId)
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->selectRaw('status, SUM(amount) as total')
            ->groupBy('status')->pluck('total', 'status');

        $paymentStatusTotals = [
            'pending'  => $paymentsByStatus['pending']  ?? 0,
            'approved' => $paymentsByStatus['approved'] ?? 0,
        ];
        $paymentStatusTotals['total'] = array_sum($paymentStatusTotals);

        // status totals للمرتجعات
        $returnsByStatus = \App\Models\SalesReturn::where('marketer_id', $marketerId)
            ->whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->selectRaw('status, SUM(total_amount) as total')
            ->groupBy('status')->pluck('total', 'status');

        $returnStatusTotals = [
            'pending'  => $returnsByStatus['pending']  ?? 0,
            'approved' => $returnsByStatus['approved'] ?? 0,
        ];
        $returnStatusTotals['total'] = array_sum($returnStatusTotals);

        // تفاصيل لكل متجر
        $storesData = [];
        $marketerStores = Store::where('marketer_id', $marketerId)->where('is_active', true)->get();
        foreach ($marketerStores as $store) {
            $sales    = (clone $salesQuery)->where('store_id', $store->id)->sum('total_amount');
            $payments = (clone $paymentsQuery)->where('store_id', $store->id)->sum('amount');
            $returns  = (clone $returnsQuery)->where('store_id', $store->id)->sum('total_amount');

            // إضافة pending
            $sales    += \App\Models\SalesInvoice::where('status', 'pending')
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date)
                ->where('store_id', $store->id)
                ->where(function($q) use ($marketerId) {
                    $q->where('marketer_id', $marketerId)->orWhere('marketer_id', 0);
                })->sum('total_amount');

            $payments += \App\Models\StorePayment::where('marketer_id', $marketerId)
                ->where('status', 'pending')
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date)
                ->where('store_id', $store->id)
                ->sum('amount');

            $returns  += \App\Models\SalesReturn::where('marketer_id', $marketerId)
                ->where('status', 'pending')
                ->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date)
                ->where('store_id', $store->id)
                ->sum('total_amount');

            $balance  = $sales - $payments - $returns;

            if ($sales > 0 || $payments > 0 || $returns > 0) {
                $storesData[] = [
                    'store_name' => $store->name,
                    'sales'      => $sales,
                    'payments'   => $payments,
                    'returns'    => $returns,
                    'balance'    => $balance,
                ];
            }
        }

        return [
            'is_summary'        => true,
            'is_store_summary'  => true,
            'total_sales'       => $totalSales + $pendingSales,
            'total_payments'    => $totalPayments + $pendingPayments,
            'total_returns'     => $totalReturns + $pendingReturns,
            'total_debt'        => $totalDebt,
            'pending_sales'     => $pendingSales,
            'pending_payments'  => $pendingPayments,
            'pending_returns'   => $pendingReturns,
            'status_totals'         => $statusTotals,
            'payment_status_totals' => $paymentStatusTotals,
            'return_status_totals'  => $returnStatusTotals,
            'stores_data'           => $storesData,
        ];
    }

    private function callProtected($object, string $method, array $args = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);
        return $reflection->invoke($object, ...$args);
    }
}
