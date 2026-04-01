<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Models\StorePayment;
use App\Models\SalesReturn;
use App\Models\CustomerInvoice;
use App\Models\CustomerPayment;
use App\Models\CustomerReturn;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CombinedSummaryController extends Controller
{
    public function index(Request $request)
    {
        $fromDate   = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate     = $request->input('to_date', now()->format('Y-m-d'));
        $storeId      = $request->input('entity_type') === 'store'    ? $request->input('store_id')    : null;
        $customerId   = $request->input('entity_type') === 'customer'  ? $request->input('customer_id') : null;
        $staffId      = $request->input('staff_id');
        $entityType   = $request->input('entity_type', 'all'); // all | store | customer
        $includeOldDebt = $request->hasAny(['from_date', 'to_date', 'export', 'pdf'])
            ? (bool) $request->input('include_old_debt', 0)
            : true;

        $rows = $this->buildRows($fromDate, $toDate, $storeId, $customerId, $staffId, $includeOldDebt, $entityType);

        if ($request->has('export')) {
            return $this->export($rows, $fromDate, $toDate, $storeId, $customerId, $staffId, $includeOldDebt, $entityType);
        }

        if ($request->has('pdf')) {
            return $this->exportPdf($rows, $fromDate, $toDate, $storeId, $customerId, $staffId, $includeOldDebt);
        }

        $grandInvoices = $rows->sum('total_invoices');
        $grandPayments = $rows->sum('total_payments');
        $grandReturns  = $rows->sum('total_returns');
        $grandOldDebt  = $rows->sum('old_debt');
        $grandDebt     = $rows->sum('total_debt');

        $storeRows    = $rows->where('type', 'متجر');
        $storeSummary = [
            'invoices'  => $storeRows->sum('total_invoices'),
            'payments'  => $storeRows->sum('total_payments'),
            'returns'   => $storeRows->sum('total_returns'),
            'old_debt'  => $storeRows->sum('old_debt'),
            'debt'      => $storeRows->sum('total_debt'),
            'pending_invoices' => SalesInvoice::where('status', 'pending')
                ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                ->when($staffId, fn($q) => $q->where('marketer_id', $staffId))
                ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->sum('total_amount'),
            'pending_payments' => StorePayment::where('status', 'pending')
                ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                ->when($staffId, fn($q) => $q->where('marketer_id', $staffId))
                ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->sum('amount'),
            'pending_returns'  => SalesReturn::where('status', 'pending')
                ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                ->when($staffId, fn($q) => $q->where('marketer_id', $staffId))
                ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->sum('total_amount'),
        ];
        $storeSummary['approved_debt'] = $storeSummary['debt'] - ($storeSummary['pending_invoices'] - $storeSummary['pending_payments'] - $storeSummary['pending_returns']);

        $customerRows    = $rows->where('type', 'عميل');
        $customerSummary = [
            'invoices' => $customerRows->sum('total_invoices'),
            'payments' => $customerRows->sum('total_payments'),
            'returns'  => $customerRows->sum('total_returns'),
            'old_debt' => $customerRows->sum('old_debt'),
            'debt'     => $customerRows->sum('total_debt'),
        ];

        $stores    = Store::orderBy('name')->get(['id', 'name']);
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $staff     = \App\Models\User::whereIn('role_id', [3, 4])->where('is_active', true)->orderBy('full_name')->get(['id', 'full_name', 'role_id']);

        return view('admin.combined-summary.index', compact(
            'rows', 'fromDate', 'toDate', 'storeId', 'customerId', 'staffId', 'includeOldDebt',
            'grandInvoices', 'grandPayments', 'grandReturns', 'grandOldDebt', 'grandDebt',
            'storeSummary', 'customerSummary',
            'stores', 'customers', 'staff'
        ));
    }

    private function buildRows($fromDate, $toDate, $storeId = null, $customerId = null, $staffId = null, $includeOldDebt = true, $entityType = 'all')
    {
        $rows = collect();

        // المتاجر — تظهر إذا entityType = all أو store
        if ($entityType !== 'customer') {
            $storeQuery = Store::orderBy('name');
            if ($storeId) $storeQuery->where('id', $storeId);

            foreach ($storeQuery->get() as $store) {
                $invoices = SalesInvoice::where('store_id', $store->id)
                    ->whereIn('status', ['approved', 'pending'])
                    ->where('marketer_id', '!=', 0)
                    ->when($staffId, fn($q) => $q->where('marketer_id', $staffId))
                    ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                    ->sum('total_amount');
                $payments = StorePayment::where('store_id', $store->id)->whereIn('status', ['approved', 'pending'])
                    ->when($staffId, fn($q) => $q->where('marketer_id', $staffId))
                    ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                    ->sum('amount');
                $returns = SalesReturn::where('store_id', $store->id)->whereIn('status', ['approved', 'pending'])
                    ->when($staffId, fn($q) => $q->where('marketer_id', $staffId))
                    ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                    ->sum('total_amount');
                $oldDebt = $includeOldDebt ? SalesInvoice::where('store_id', $store->id)->where('marketer_id', 0)->sum('total_amount') : 0;

                if ($invoices == 0 && $payments == 0 && $returns == 0 && $oldDebt == 0) continue;

                $rows->push((object)[
                    'name'           => $store->name,
                    'type'           => 'متجر',
                    'total_invoices' => $invoices,
                    'total_payments' => $payments,
                    'total_returns'  => $returns,
                    'old_debt'       => $oldDebt,
                    'total_debt'     => $invoices - $payments - $returns + $oldDebt,
                ]);
            }
        }

        // العملاء — تظهر إذا entityType = all أو customer
        $staffIsMarketer = $staffId && \App\Models\User::where('id', $staffId)->value('role_id') == 3;
        if ($entityType !== 'store' && !$staffIsMarketer) {
            $customerQuery = Customer::orderBy('name');
            if ($customerId) $customerQuery->where('id', $customerId);

            foreach ($customerQuery->get() as $customer) {
                $invoices = CustomerInvoice::where('customer_id', $customer->id)
                    ->where('status', 'completed')
                    ->where('sales_user_id', '!=', 0)
                    ->when($staffId, fn($q) => $q->where('sales_user_id', $staffId))
                    ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                    ->sum('total_amount');
                $payments = CustomerPayment::where('customer_id', $customer->id)->where('status', 'completed')
                    ->when($staffId, fn($q) => $q->where('sales_user_id', $staffId))
                    ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                    ->sum('amount');
                $returns = CustomerReturn::where('customer_id', $customer->id)->where('status', 'completed')
                    ->when($staffId, fn($q) => $q->where('sales_user_id', $staffId))
                    ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                    ->sum('total_amount');
                $oldDebt = $includeOldDebt ? CustomerInvoice::where('customer_id', $customer->id)->where('sales_user_id', 0)->sum('total_amount') : 0;

                if ($invoices == 0 && $payments == 0 && $returns == 0 && $oldDebt == 0) continue;

                $rows->push((object)[
                    'name'           => $customer->name,
                    'type'           => 'عميل',
                    'total_invoices' => $invoices,
                    'total_payments' => $payments,
                    'total_returns'  => $returns,
                    'old_debt'       => $oldDebt,
                    'total_debt'     => $invoices - $payments - $returns + $oldDebt,
                ]);
            }
        }

        return $rows->sortByDesc('total_debt')->values();
    }

    private function exportPdf($rows, $fromDate, $toDate, $storeId = null, $customerId = null, $staffId = null, $includeOldDebt = true)
    {
        $arabic = new \ArPHP\I18N\Arabic();
        $g  = fn($text) => $arabic->utf8Glyphs($text);
        $en = fn($str)  => str_replace(
            ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'],
            ['0','1','2','3','4','5','6','7','8','9'], $str
        );

        $grandInvoices = $rows->sum('total_invoices');
        $grandPayments = $rows->sum('total_payments');
        $grandReturns  = $rows->sum('total_returns');
        $grandDebt     = $rows->sum('total_debt');

        $processedRows = $rows->map(fn($row) => (object)[
            'name'           => $en($g($row->name)),
            'type'           => $g($row->type),
            'is_store'       => $row->type === 'متجر',
            'total_invoices' => $row->total_invoices,
            'total_payments' => $row->total_payments,
            'total_returns'  => $row->total_returns,
            'old_debt'       => $row->old_debt,
            'total_debt'     => $row->total_debt,
        ]);

        $labels = [
            'title'          => $g('الملخص المالي الشامل'),
            'grandTotal'     => $g('الإجماليات الكلية'),
            'stores'         => $g('المتاجر'),
            'customers'      => $g('العملاء'),
            'invoices'       => $g('إجمالي الفواتير'),
            'payments'       => $g('إجمالي المدفوعات'),
            'returns'        => $g('إجمالي المرتجعات'),
            'old_debt'       => $g('ديون سابقة'),
            'debt'           => $g('الدين الحالي'),
            'debtStatus'     => $g('دائن / مدين'),
            'name'           => $g('الاسم'),
            'type'           => $g('النوع'),
            'store'          => $g('متجر'),
            'customer'       => $g('عميل'),
            'total'          => $g('الإجمالي'),
            'debtor'         => $g('مدين'),
            'creditor'       => $g('دائن'),
            'currency'       => $g('د.ل'),
            'dateFrom'       => $fromDate,
            'dateTo'         => $toDate,
            'labelFrom'      => $g('من'),
            'labelTo'        => $g('إلى'),
            'filterStaff'      => $staffId    ? $en($g(\App\Models\User::find($staffId)?->full_name ?? '')) : null,
            'filterStore'      => $storeId    ? $en($g(Store::find($storeId)?->name ?? ''))                : null,
            'filterCustomer'   => $customerId ? $en($g(Customer::find($customerId)?->name ?? ''))          : null,
            'filterLabel'      => $g('الفلاتر المطبقة'),
            'filterStaffLabel'    => $g('الموظف'),
            'filterStoreLabel'    => $g('المتجر'),
            'filterCustomerLabel' => $g('العميل'),
            'filterOldDebt'       => $staffId ? ($includeOldDebt ? $g('مضمنة') : $g('غير مضمنة')) : null,
            'filterOldDebtLabel'  => $g('الديون السابقة'),
            'showOldDebt'         => $includeOldDebt,
        ];

        $viewData = compact('processedRows', 'fromDate', 'toDate', 'grandInvoices', 'grandPayments', 'grandReturns', 'grandDebt', 'labels', 'rows');

        $options = ['isRemoteEnabled' => false, 'isHtml5ParserEnabled' => true, 'isFontSubsettingEnabled' => true, 'compress' => 1, 'dpi' => 96];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.combined-summary.pdf', $viewData)->setPaper('a4');
        foreach ($options as $k => $v) $pdf->setOption($k, $v);
        $pdf->render();
        $labels['totalPages'] = $pdf->getDomPDF()->getCanvas()->get_page_count();

        $viewData['labels'] = $labels;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.combined-summary.pdf', $viewData)->setPaper('a4');
        foreach ($options as $k => $v) $pdf->setOption($k, $v);

        return $pdf->stream('combined-summary-' . $fromDate . '-' . $toDate . '.pdf');
    }

    private function export($rows, $fromDate, $toDate, $storeId = null, $customerId = null, $staffId = null, $includeOldDebt = true, $entityType = 'all')
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $row = 1;

        // معلومات الفلاتر
        $infoRows = [['من تاريخ', $fromDate], ['إلى تاريخ', $toDate]];
        if ($entityType !== 'all') $infoRows[] = ['عرض', match($entityType) { 'store' => 'المتاجر فقط', 'customer' => 'العملاء فقط', default => 'الكل' }];
        if ($staffId)    $infoRows[] = ['الموظف',  \App\Models\User::find($staffId)?->full_name ?? ''];
        if ($staffId)    $infoRows[] = ['الديون السابقة', $includeOldDebt ? 'مضمنة' : 'غير مضمنة'];
        if ($storeId)    $infoRows[] = ['المتجر',   Store::find($storeId)?->name ?? ''];
        if ($customerId) $infoRows[] = ['العميل',   Customer::find($customerId)?->name ?? ''];

        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $row, $info[0]);
            $sheet->setCellValue('B' . $row, $info[1]);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                'font'    => ['bold' => true, 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
        }
        $row++;

        // ملخص المتاجر
        $storeRows  = $rows->where('type', 'متجر');
        $pendingInv = SalesInvoice::where('status', 'pending')
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->when($staffId, fn($q) => $q->where('marketer_id', $staffId))
            ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->sum('total_amount');
        $pendingPay = StorePayment::where('status', 'pending')
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->when($staffId, fn($q) => $q->where('marketer_id', $staffId))
            ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->sum('amount');
        $pendingRet = SalesReturn::where('status', 'pending')
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->when($staffId, fn($q) => $q->where('marketer_id', $staffId))
            ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->sum('total_amount');

        $sheet->setCellValue('A' . $row, 'ملخص المتاجر');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        $sheet->fromArray(['ديون سابقة', 'المبيعات', 'المدفوعات', 'المرتجعات', 'إجمالي الدين', 'فواتير معلقة', 'إيصالات معلقة'], null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BBDEFB']],
            'font'      => ['bold' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        $storeDebt    = $storeRows->sum('total_debt');
        $storeOldDebt = $storeRows->sum('old_debt');
        $sheet->fromArray([
            number_format($storeOldDebt, 2),
            number_format($storeRows->sum('total_invoices'), 2),
            number_format($storeRows->sum('total_payments'), 2),
            number_format($storeRows->sum('total_returns'), 2),
            number_format($storeDebt, 2),
            number_format($pendingInv, 2),
            number_format($pendingPay, 2),
        ], null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
            'font'      => ['bold' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('E' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $storeDebt > 0 ? 'FFCDD2' : 'C8E6C9']],
            'font' => ['bold' => true, 'color' => ['rgb' => $storeDebt > 0 ? 'C62828' : '2E7D32']],
        ]);
        $row += 2;

        // ملخص العملاء
        $customerRows = $rows->where('type', 'عميل');
        $sheet->setCellValue('A' . $row, 'ملخص العملاء');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6A1B9A']],
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        $sheet->fromArray(['ديون سابقة', 'المبيعات', 'المدفوعات', 'المرتجعات', 'إجمالي الدين'], null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E1BEE7']],
            'font'      => ['bold' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        $custDebt    = $customerRows->sum('total_debt');
        $custOldDebt = $customerRows->sum('old_debt');
        $sheet->fromArray([
            number_format($custOldDebt, 2),
            number_format($customerRows->sum('total_invoices'), 2),
            number_format($customerRows->sum('total_payments'), 2),
            number_format($customerRows->sum('total_returns'), 2),
            number_format($custDebt, 2),
        ], null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
            'font'      => ['bold' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('E' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $custDebt > 0 ? 'FFCDD2' : 'C8E6C9']],
            'font' => ['bold' => true, 'color' => ['rgb' => $custDebt > 0 ? 'C62828' : '2E7D32']],
        ]);
        $row += 2;

        // رؤوس الأعمدة
        $headers = ['الاسم', 'النوع'];
        if ($includeOldDebt) $headers[] = 'ديون سابقة';
        array_push($headers, 'إجمالي الفواتير', 'إجمالي المدفوعات', 'إجمالي المرتجعات', 'الدين الحالي', 'دائن / مدين');
        $lastCol = $includeOldDebt ? 'H' : 'G';
        $debtCol = $includeOldDebt ? 'G' : 'F';
        $statusCol = $lastCol;

        $sheet->fromArray($headers, null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        foreach ($rows as $item) {
            $typeColor = $item->type === 'متجر' ? 'E3F2FD' : 'F3E5F5';
            $debtColor = $item->total_debt > 0 ? 'FFCDD2' : ($item->total_debt < 0 ? 'C8E6C9' : 'F5F5F5');
            $debtLabel = $item->total_debt > 0 ? 'مدين' : ($item->total_debt < 0 ? 'دائن' : '--');

            $rowData = [$item->name, $item->type];
            if ($includeOldDebt) $rowData[] = number_format($item->old_debt, 2);
            array_push($rowData,
                number_format($item->total_invoices, 2),
                number_format($item->total_payments, 2),
                number_format($item->total_returns, 2),
                number_format($item->total_debt, 2),
                $debtLabel
            );

            $sheet->fromArray($rowData, null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);
            $sheet->getStyle('B' . $row)->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $typeColor]],
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            if ($includeOldDebt) {
                $sheet->getStyle('C' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $item->old_debt > 0 ? 'FFF8E1' : 'F5F5F5']],
                    'font' => ['bold' => $item->old_debt > 0, 'color' => ['rgb' => $item->old_debt > 0 ? 'E65100' : '9E9E9E']],
                ]);
            }
            $sheet->getStyle($debtCol . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $debtColor]],
                'font' => ['bold' => true],
            ]);
            $hColor = $item->total_debt < 0 ? ['bg' => '4CAF50', 'fg' => 'FFFFFF'] : ($item->total_debt > 0 ? ['bg' => 'FFCDD2', 'fg' => 'C62828'] : null);
            if ($hColor) {
                $sheet->getStyle($statusCol . $row)->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $hColor['bg']]],
                    'font'      => ['bold' => true, 'color' => ['rgb' => $hColor['fg']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
            $row++;
        }

        // الإجماليات
        $row++;
        $grandDebt    = $rows->sum('total_debt');
        $grandOldDebt = $rows->sum('old_debt');
        $totalRow = ['الإجمالي', ''];
        if ($includeOldDebt) $totalRow[] = number_format($grandOldDebt, 2);
        array_push($totalRow,
            number_format($rows->sum('total_invoices'), 2),
            number_format($rows->sum('total_payments'), 2),
            number_format($rows->sum('total_returns'), 2),
            number_format($grandDebt, 2),
            $grandDebt > 0 ? 'مدين' : ($grandDebt < 0 ? 'دائن' : '--')
        );
        $sheet->fromArray($totalRow, null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8EAF6']],
            'font'    => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $gColor = $grandDebt < 0 ? ['bg' => '4CAF50', 'fg' => 'FFFFFF'] : ($grandDebt > 0 ? ['bg' => 'FFCDD2', 'fg' => 'C62828'] : null);
        if ($gColor) {
            $sheet->getStyle($statusCol . $row)->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $gColor['bg']]],
                'font'      => ['bold' => true, 'color' => ['rgb' => $gColor['fg']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'الملخص_المالي_الشامل_' . $fromDate . '_' . $toDate . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }
}
