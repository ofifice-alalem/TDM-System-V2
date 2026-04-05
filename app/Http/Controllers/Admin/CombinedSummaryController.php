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
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CombinedSummaryController extends Controller
{
    public function index(Request $request)
    {
        $tab        = $request->input('tab', 'financial'); // financial | clients
        $fromDate   = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate     = $request->input('to_date', now()->format('Y-m-d'));
        $storeId      = $request->input('entity_type') === 'store'    ? $request->input('store_id')    : null;
        $storeName    = $request->input('entity_type') === 'store'    ? $request->input('store_name')   : null;
        $customerId   = $request->input('entity_type') === 'customer'  ? $request->input('customer_id') : null;
        $customerName = $request->input('entity_type') === 'customer'  ? $request->input('customer_name') : null;
        $staffId      = $request->input('staff_id');
        $entityType   = $request->input('entity_type', 'all'); // all | store | customer
        $productId    = $request->input('product_id');
        $sortBy       = $request->input('sort_by', 'amount'); // amount | qty
        $includeOldDebt = $request->hasAny(['from_date', 'to_date', 'export', 'pdf'])
            ? (bool) $request->input('include_old_debt', 0)
            : true;

        // تاب الملخص الشامل لكل زبون
        if ($tab === 'clients') {
            $clientsData = $this->buildClientProductData($fromDate, $toDate, $storeId, $customerId, $productId, $entityType, $storeName, $customerName, $staffId);

            if ($request->has('export')) {
                return $this->exportClientsExcel($clientsData, $fromDate, $toDate, $productId, $sortBy, $entityType, $storeName, $customerName, $staffId);
            }
            if ($request->has('pdf')) {
                return $this->exportClientsPdf($clientsData, $fromDate, $toDate, $productId, $entityType, $storeName, $customerName, $staffId);
            }

            usort($clientsData, function($a, $b) use ($sortBy) {
                return $sortBy === 'qty'
                    ? $b['total_qty'] <=> $a['total_qty']
                    : $b['total_amount'] <=> $a['total_amount'];
            });

            $products  = Product::orderBy('name')->get(['id', 'name']);
            $stores    = Store::orderBy('name')->get(['id', 'name']);
            $customers = Customer::orderBy('name')->get(['id', 'name']);
            $staff     = \App\Models\User::whereIn('role_id', [3, 4])->where('is_active', true)->orderBy('full_name')->get(['id', 'full_name', 'role_id']);

            return view('admin.combined-summary.index', compact(
                'tab', 'fromDate', 'toDate', 'entityType', 'storeId', 'customerId', 'productId', 'sortBy',
                'clientsData', 'stores', 'customers', 'staff', 'products',
                // dummy vars for financial tab (not used)
                'staffId', 'includeOldDebt'
            ) + [
                'rows' => collect(), 'grandInvoices' => 0, 'grandPayments' => 0,
                'grandReturns' => 0, 'grandOldDebt' => 0, 'grandDebt' => 0,
                'storeSummary' => ['invoices'=>0,'payments'=>0,'returns'=>0,'old_debt'=>0,'debt'=>0,'pending_invoices'=>0,'pending_payments'=>0,'pending_returns'=>0,'approved_debt'=>0],
                'customerSummary' => ['invoices'=>0,'payments'=>0,'returns'=>0,'old_debt'=>0,'debt'=>0],
            ]);
        }

        $rows = $this->buildRows($fromDate, $toDate, $storeId, $customerId, $staffId, $includeOldDebt, $entityType, $storeName, $customerName);

        if ($request->has('export')) {
            return $this->export($rows, $fromDate, $toDate, $storeId, $customerId, $staffId, $includeOldDebt, $entityType, $storeName, $customerName);
        }

        if ($request->has('pdf')) {
            return $this->exportPdf($rows, $fromDate, $toDate, $storeId, $customerId, $staffId, $includeOldDebt, $entityType, $storeName, $customerName);
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
        $products  = Product::orderBy('name')->get(['id', 'name']);

        return view('admin.combined-summary.index', compact(
            'tab', 'rows', 'fromDate', 'toDate', 'storeId', 'customerId', 'staffId', 'includeOldDebt',
            'grandInvoices', 'grandPayments', 'grandReturns', 'grandOldDebt', 'grandDebt',
            'storeSummary', 'customerSummary',
            'stores', 'customers', 'staff', 'products', 'entityType', 'productId', 'sortBy'
        ) + ['clientsData' => null]);
    }

    private function buildClientProductData(
        string $fromDate, string $toDate,
        ?int $storeId = null, ?int $customerId = null, ?string $productId = null,
        string $entityType = 'all', ?string $storeName = null, ?string $customerName = null, $staffId = null
    ): array {
        $result = [];

        if ($entityType !== 'customer') {
            $storeQuery = Store::orderBy('name');
            if ($storeId) $storeQuery->where('id', $storeId);
            elseif ($storeName) $storeQuery->where('name', 'like', '%' . $storeName . '%');

            foreach ($storeQuery->get() as $store) {
                $items = DB::table('sales_invoice_items as i')
                    ->join('sales_invoices as inv', 'inv.id', '=', 'i.invoice_id')
                    ->join('products as p', 'p.id', '=', 'i.product_id')
                    ->whereIn('inv.status', ['approved', 'pending'])
                    ->where('inv.store_id', $store->id)
                    ->when($staffId, fn($q) => $q->where('inv.marketer_id', $staffId))
                    ->whereDate('inv.created_at', '>=', $fromDate)
                    ->whereDate('inv.created_at', '<=', $toDate)
                    ->when($productId, fn($q) => $q->where('i.product_id', $productId))
                    ->select('i.product_id', 'p.name as product_name', 'i.unit_price', 'i.quantity')
                    ->get();

                if ($items->isEmpty()) continue;

                $result[] = [
                    'id'           => $store->id,
                    'name'         => $store->name,
                    'type'         => 'متجر',
                    'products'     => $this->groupByProduct($items),
                    'total_qty'    => $items->sum('quantity'),
                    'total_amount' => round($items->sum(fn($i) => $i->unit_price * $i->quantity), 2),
                ];
            }
        }

        if ($entityType !== 'store') {
            $customerQuery = Customer::orderBy('name');
            if ($customerId) $customerQuery->where('id', $customerId);
            elseif ($customerName) $customerQuery->where('name', 'like', '%' . $customerName . '%');

            $staffIsMarketer = $staffId && \App\Models\User::where('id', $staffId)->value('role_id') == 3;
            if (!$staffIsMarketer) {
                foreach ($customerQuery->get() as $customer) {
                    $items = DB::table('customer_invoice_items as i')
                        ->join('customer_invoices as inv', 'inv.id', '=', 'i.invoice_id')
                        ->join('products as p', 'p.id', '=', 'i.product_id')
                        ->where('inv.status', 'completed')
                        ->where('inv.customer_id', $customer->id)
                        ->when($staffId, fn($q) => $q->where('inv.sales_user_id', $staffId))
                        ->whereDate('inv.created_at', '>=', $fromDate)
                        ->whereDate('inv.created_at', '<=', $toDate)
                        ->when($productId, fn($q) => $q->where('i.product_id', $productId))
                        ->select('i.product_id', 'p.name as product_name', 'i.unit_price', 'i.quantity')
                        ->get();

                    if ($items->isEmpty()) continue;

                    $result[] = [
                        'id'           => $customer->id,
                        'name'         => $customer->name,
                        'type'         => 'عميل',
                        'products'     => $this->groupByProduct($items),
                        'total_qty'    => $items->sum('quantity'),
                        'total_amount' => round($items->sum(fn($i) => $i->unit_price * $i->quantity), 2),
                    ];
                }
            }
        }

        return $result;
    }

    private function exportClientsPdf(array $clientsData, string $fromDate, string $toDate, ?string $productId, string $entityType = 'all', ?string $storeName = null, ?string $customerName = null, $staffId = null)
    {
        $arabic = new \ArPHP\I18N\Arabic();
        $g  = fn($text) => $arabic->utf8Glyphs($text);
        $en = fn($str)  => str_replace(
            ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'],
            ['0','1','2','3','4','5','6','7','8','9'], $str
        );

        $entries = array_map(function($entry) use ($g, $en) {
            return [
                'name'         => $en($g($entry['name'])),
                'type'         => $g($entry['type']),
                'color'        => $entry['type'] === 'متجر' ? '1565C0' : '6A1B9A',
                'total_qty'    => $entry['total_qty'],
                'total_amount' => $entry['total_amount'],
                'products'     => array_map(function($product) use ($g, $en) {
                    return [
                        'product_name' => $en($g($product['product_name'])),
                        'avg_price'    => $product['avg_price'],
                        'total_qty'    => $product['total_qty'],
                        'total_amount' => $product['total_amount'],
                        'times'        => array_sum(array_column($product['prices'], 'times')),
                        'prices'       => $product['prices'],
                    ];
                }, $entry['products']),
            ];
        }, $clientsData);

        $entityLabel = match($entityType) {
            'store'    => $g('المتاجر فقط'),
            'customer' => $g('العملاء فقط'),
            default    => null,
        };

        $labels = [
            'title'        => $g('الملخص الشامل لكل زبون'),
            'product'      => $g('المنتج'),
            'price'        => $g('السعر'),
            'avg'          => $g('متوسط:'),
            'times'        => $g('مرات الشراء'),
            'qty'          => $g('الكمية'),
            'amount'       => $g('المبلغ'),
            'total'        => $g('الإجمالي'),
            'type'         => $g('النوع'),
            'from'         => $g('من'),
            'to'           => $g('إلى'),
            'filterProd'   => $productId ? $en($g(Product::find($productId)?->name ?? '')) : null,
            'filterEntity'      => $entityLabel,
            'filterEntityLabel' => $g('عرض'),
            'filterSearch'      => $storeName ? $en($g($storeName)) : ($customerName ? $en($g($customerName)) : null),
            'filterStaff'       => $staffId ? $en($g(\App\Models\User::find($staffId)?->full_name ?? '')) : null,
            'filterSearchLabel' => $g('بحث'),
            'dateFrom'          => $fromDate,
            'dateTo'            => $toDate,
            'totalPages'        => 1,
        ];

        $options = ['isRemoteEnabled' => false, 'isHtml5ParserEnabled' => true, 'isFontSubsettingEnabled' => true, 'compress' => 1, 'dpi' => 96];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.combined-summary.client-products-pdf', compact('entries', 'labels'))->setPaper('a4');
        foreach ($options as $k => $v) $pdf->setOption($k, $v);
        $pdf->render();
        $labels['totalPages'] = $pdf->getDomPDF()->getCanvas()->get_page_count();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.combined-summary.client-products-pdf', compact('entries', 'labels'))->setPaper('a4');
        foreach ($options as $k => $v) $pdf->setOption($k, $v);

        return $pdf->stream('client-products-' . $fromDate . '-' . $toDate . '.pdf');
    }

    private function exportClientsExcel(array $clientsData, string $fromDate, string $toDate, ?string $productId, string $sortBy, string $entityType = 'all', ?string $storeName = null, ?string $customerName = null, $staffId = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $row = 1;
        $infoRows = [['من تاريخ', $fromDate], ['إلى تاريخ', $toDate]];
        $infoRows[] = ['عرض', match($entityType) { 'store' => 'المتاجر فقط', 'customer' => 'العملاء فقط', default => 'الكل' }];
        if ($staffId)      $infoRows[] = ['الموظف', \App\Models\User::find($staffId)?->full_name ?? ''];
        if ($storeName)    $infoRows[] = ['بحث (متجر)', $storeName];
        if ($customerName) $infoRows[] = ['بحث (عميل)', $customerName];
        if ($productId)    $infoRows[] = ['المنتج', Product::find($productId)?->name ?? $productId];
        $infoRows[] = ['ترتيب حسب', $sortBy === 'qty' ? 'الكمية' : 'المبلغ'];

        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $row, $info[0]);
            $sheet->setCellValue('B' . $row, $info[1]);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                'font'    => ['bold' => true, 'size' => 11],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
        }
        $row++;

        foreach ($clientsData as $entry) {
            $isStore = $entry['type'] === 'متجر';
            $color   = $isStore ? '1565C0' : '6A1B9A';
            $bgHead  = $isStore ? 'BBDEFB' : 'E1BEE7';

            $sheet->setCellValue('A' . $row, $entry['name'] . ' — ' . $entry['type']);
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;

            $sheet->fromArray(['المنتج', 'السعر', 'مرات الشراء', 'الكمية', 'المبلغ'], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgHead]],
                'font'      => ['bold' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;

            foreach ($entry['products'] as $product) {
                $sheet->fromArray([
                    $product['product_name'],
                    'متوسط: ' . number_format($product['avg_price'], 2),
                    array_sum(array_column($product['prices'], 'times')),
                    number_format($product['total_qty']),
                    number_format($product['total_amount'], 2),
                ], null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
                    'font'    => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row++;

                foreach ($product['prices'] as $i => $price) {
                    $sheet->fromArray([
                        'سعر ' . ($i + 1),
                        number_format($price['price'], 2),
                        $price['times'],
                        number_format($price['total_qty']),
                        number_format($price['total_amount'], 2),
                    ], null, 'A' . $row);
                    $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $sheet->getStyle('B' . $row)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF8E1']],
                        'font' => ['bold' => true, 'color' => ['rgb' => 'E65100']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $row++;
                }
            }

            $sheet->fromArray([
                'الإجمالي', '', '',
                number_format($entry['total_qty']),
                number_format($entry['total_amount'], 2),
            ], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8EAF6']],
                'font'    => ['bold' => true, 'size' => 11],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row += 2;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'ملخص_الزبائن_' . $fromDate . '_' . $toDate . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    private function groupByProduct($items): array
    {
        $grouped  = $items->groupBy('product_id');
        $products = [];

        foreach ($grouped as $productId => $pItems) {
            $priceGroups = $pItems->groupBy(fn($i) => number_format((float)$i->unit_price, 2));
            $prices = [];

            foreach ($priceGroups as $price => $priceItems) {
                $prices[] = [
                    'price'        => (float) $price,
                    'times'        => $priceItems->count(),
                    'total_qty'    => $priceItems->sum('quantity'),
                    'total_amount' => $priceItems->sum(fn($i) => $i->unit_price * $i->quantity),
                ];
            }

            usort($prices, fn($a, $b) => $a['price'] <=> $b['price']);

            $totalQty    = array_sum(array_column($prices, 'total_qty'));
            $totalAmount = array_sum(array_column($prices, 'total_amount'));

            $products[] = [
                'product_id'   => $productId,
                'product_name' => $pItems->first()->product_name,
                'prices'       => $prices,
                'total_qty'    => $totalQty,
                'avg_price'    => $totalQty > 0 ? round($totalAmount / $totalQty, 2) : 0,
                'total_amount' => round($totalAmount, 2),
            ];
        }

        usort($products, fn($a, $b) => $b['total_amount'] <=> $a['total_amount']);

        return $products;
    }

    private function buildRows($fromDate, $toDate, $storeId = null, $customerId = null, $staffId = null, $includeOldDebt = true, $entityType = 'all', $storeName = null, $customerName = null)
    {
        $rows = collect();

        // المتاجر — تظهر إذا entityType = all أو store
        if ($entityType !== 'customer') {
            $storeQuery = Store::orderBy('name');
            if ($storeId) $storeQuery->where('id', $storeId);
            elseif ($storeName) $storeQuery->where('name', 'like', '%' . $storeName . '%');

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
            elseif ($customerName) $customerQuery->where('name', 'like', '%' . $customerName . '%');

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

    private function exportPdf($rows, $fromDate, $toDate, $storeId = null, $customerId = null, $staffId = null, $includeOldDebt = true, $entityType = 'all', ?string $storeName = null, ?string $customerName = null)
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
            'filterStore'      => $storeId    ? $en($g(Store::find($storeId)?->name ?? ''))       : ($storeName    ? $en($g($storeName))    : null),
            'filterCustomer'   => $customerId ? $en($g(Customer::find($customerId)?->name ?? '')) : ($customerName ? $en($g($customerName)) : null),
            'filterLabel'      => $g('الفلاتر المطبقة'),
            'filterStaffLabel'    => $g('الموظف'),
            'filterStoreLabel'    => $g('المتجر'),
            'filterCustomerLabel' => $g('العميل'),
            'filterOldDebt'       => $staffId ? ($includeOldDebt ? $g('مضمنة') : $g('غير مضمنة')) : null,
            'filterOldDebtLabel'  => $g('الديون السابقة'),
            'filterEntity'        => match($entityType) {
                'store'    => $g('المتاجر فقط'),
                'customer' => $g('العملاء فقط'),
                default    => null,
            },
            'filterEntityLabel'   => $g('عرض'),
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

    private function export($rows, $fromDate, $toDate, $storeId = null, $customerId = null, $staffId = null, $includeOldDebt = true, $entityType = 'all', ?string $storeName = null, ?string $customerName = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $row = 1;

        // معلومات الفلاتر
        $infoRows = [['من تاريخ', $fromDate], ['إلى تاريخ', $toDate]];
        $infoRows[] = ['عرض', match($entityType) { 'store' => 'المتاجر فقط', 'customer' => 'العملاء فقط', default => 'الكل' }];
        if ($staffId)    $infoRows[] = ['الموظف',  \App\Models\User::find($staffId)?->full_name ?? ''];
        if ($staffId)    $infoRows[] = ['الديون السابقة', $includeOldDebt ? 'مضمنة' : 'غير مضمنة'];
        if ($storeId)    $infoRows[] = ['المتجر',   Store::find($storeId)?->name ?? ''];
        elseif ($storeName) $infoRows[] = ['بحث (متجر)', $storeName];
        if ($customerId) $infoRows[] = ['العميل',   Customer::find($customerId)?->name ?? ''];
        elseif ($customerName) $infoRows[] = ['بحث (عميل)', $customerName];

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
