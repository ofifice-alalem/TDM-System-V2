<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StaffPricingController extends Controller
{
    public function index(Request $request)
    {
        $fromDate  = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate    = $request->input('to_date', now()->format('Y-m-d'));
        $productId = $request->input('product_id');
        $sortBy    = $request->input('sort_by', 'amount'); // amount | qty
        $mode      = $request->input('mode', 'single');    // single | compare

        $marketers   = User::where('role_id', 3)->where('is_active', true)->orderBy('full_name')->get();
        $salesUsers  = User::where('role_id', 4)->where('is_active', true)->orderBy('full_name')->get();
        $products    = Product::orderBy('name')->get();

        $staffData   = null;
        $compareData = null;

        if ($mode === 'single' && $request->filled('user_id')) {
            if ($request->user_id === 'all') {
                $staffData = $this->buildAllStaffData($fromDate, $toDate, $productId);
            } else {
                $staffData = $this->buildStaffData(
                    (int) $request->user_id, $fromDate, $toDate, $productId
                );
            }
        }

        if ($mode === 'compare') {
            $compareData = $this->buildCompareData($fromDate, $toDate, $productId, $sortBy);
        }

        if ($request->has('export')) {
            return $this->export($mode, $staffData, $compareData, $fromDate, $toDate, $productId, $sortBy);
        }

        if ($request->has('pdf')) {
            return $this->exportPdf($mode, $staffData, $compareData, $fromDate, $toDate, $productId, $sortBy);
        }

        return view('admin.staff-pricing.index', compact(
            'marketers', 'salesUsers', 'products',
            'fromDate', 'toDate', 'productId', 'sortBy', 'mode',
            'staffData', 'compareData'
        ));
    }

    private function exportPdf(string $mode, ?array $staffData, ?array $compareData, string $fromDate, string $toDate, ?string $productId = null, ?string $sortBy = null)
    {
        $arabic = new \ArPHP\I18N\Arabic();
        $g  = fn($text) => $arabic->utf8Glyphs($text);
        $en = fn($str)  => str_replace(
            ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'],
            ['0','1','2','3','4','5','6','7','8','9'], $str
        );

        $rawEntries = $mode === 'compare' ? ($compareData ?? []) : ($staffData ? [$staffData] : []);

        $entries = array_map(function($entry) use ($g, $en) {
            $user  = $entry['user'];
            $isAll = $user === null;
            $isM   = !$isAll && $user->role_id === 3;
            return [
                'name'         => $isAll ? $g('جميع الموظفين') : $en($g($user->full_name)),
                'role'         => $isAll ? $g('الكل') : ($isM ? $g('مسوق') : $g('مبيعات')),
                'color'        => $isAll ? '7B1FA2' : ($isM ? '1565C0' : '2E7D32'),
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
        }, $rawEntries);

        $grandQty    = array_sum(array_column($entries, 'total_qty'));
        $grandAmount = array_sum(array_column($entries, 'total_amount'));

        $labels = [
            'title'        => $g('معدل الموظفين'),
            'product'      => $g('المنتج'),
            'price'        => $g('السعر'),
            'avg'          => $g('متوسط:'),
            'times'        => $g('مرات البيع'),
            'qty'          => $g('الكمية'),
            'amount'       => $g('المبلغ'),
            'total'        => $g('الإجمالي'),
            'labelFrom'    => $g('من'),
            'labelTo'      => $g('إلى'),
            'filterProd'   => $productId ? $en($g(Product::find($productId)?->name ?? '')) : null,
            'filterProdLabel' => $g('المنتج'),
            'allProducts'     => $g('كل المنتجات'),
            'filterSort'      => ($mode === 'compare' && $sortBy) ? $g($sortBy === 'qty' ? 'ترتيب: الكمية' : 'ترتيب: المبلغ') : null,
            'filterSortLabel' => $g('ترتيب'),
            'staffLabel'      => $g('الموظف'),
            'allStaff'        => $g('الكل'),
            'grandQty'     => $grandQty,
            'grandAmount'  => $grandAmount,
            'dateFrom'     => $fromDate,
            'dateTo'       => $toDate,
            'totalPages'   => 1,
        ];

        $view = 'admin.staff-pricing.pdf';
        $options = ['isRemoteEnabled' => false, 'isHtml5ParserEnabled' => true, 'isFontSubsettingEnabled' => true, 'compress' => 1, 'dpi' => 96];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, compact('entries', 'labels'))->setPaper('a4');
        foreach ($options as $k => $v) $pdf->setOption($k, $v);
        $pdf->render();
        $labels['totalPages'] = $pdf->getDomPDF()->getCanvas()->get_page_count();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, compact('entries', 'labels'))->setPaper('a4');
        foreach ($options as $k => $v) $pdf->setOption($k, $v);

        return $pdf->stream('staff-pricing-' . $fromDate . '_' . $toDate . '.pdf');
    }

    private function export(string $mode, ?array $staffData, ?array $compareData, string $fromDate, string $toDate, ?string $productId = null, ?string $sortBy = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $row = 1;

        // معلومات الفترة والفلاتر
        $infoRows = [['من تاريخ', $fromDate], ['إلى تاريخ', $toDate]];
        if ($productId) {
            $productName = Product::find($productId)?->name ?? $productId;
            $infoRows[] = ['المنتج', $productName];
        }
        if ($mode === 'compare' && $sortBy) {
            $infoRows[] = ['ترتيب حسب', $sortBy === 'qty' ? 'الكمية' : 'المبلغ'];
        }
        foreach ($infoRows as $info) {
            $sheet->setCellValue('A' . $row, $info[0]);
            $sheet->setCellValue('B' . $row, $info[1]);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                'font' => ['bold' => true, 'size' => 11],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
        }
        $row++;

        $entries = $mode === 'compare' ? ($compareData ?? []) : ($staffData ? [$staffData] : []);

        foreach ($entries as $entry) {
            $user   = $entry['user'];
            $isAll  = $user === null;
            $name   = $isAll ? 'جميع الموظفين' : $user->full_name;
            $role   = $isAll ? 'الكل' : ($user->role_id === 3 ? 'مسوق' : 'مبيعات');
            $color  = $isAll ? '7B1FA2' : ($user->role_id === 3 ? '1565C0' : '2E7D32');
            $bgHead = $isAll ? 'E1BEE7' : ($user->role_id === 3 ? 'BBDEFB' : 'C8E6C9');

            // اسم الموظف
            $sheet->setCellValue('A' . $row, $name . ' — ' . $role);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;

            // رؤوس الأعمدة
            $sheet->fromArray(['المنتج', 'السعر', 'مرات البيع', 'الكمية', 'المبلغ'], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgHead]],
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;

            foreach ($entry['products'] as $product) {
                // صف المنتج الرئيسي
                $sheet->fromArray([
                    $product['product_name'],
                    'متوسط: ' . number_format($product['avg_price'], 2),
                    array_sum(array_column($product['prices'], 'times')),
                    number_format($product['total_qty']),
                    number_format($product['total_amount'], 2),
                ], null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row++;

                // صفوف الأسعار
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

            // إجمالي الموظف
            $sheet->fromArray([
                'الإجمالي', '', '',
                number_format($entry['total_qty']),
                number_format($entry['total_amount'], 2),
            ], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8EAF6']],
                'font' => ['bold' => true, 'size' => 11],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row += 2;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'معدل_الموظفين_' . $fromDate . '_' . $toDate . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    private function buildAllStaffData(string $fromDate, string $toDate, ?string $productId): array
    {
        $storeItems = DB::table('sales_invoice_items as i')
            ->join('sales_invoices as inv', 'inv.id', '=', 'i.invoice_id')
            ->join('products as p', 'p.id', '=', 'i.product_id')
            ->whereIn('inv.status', ['approved', 'pending'])
            ->whereDate('inv.created_at', '>=', $fromDate)
            ->whereDate('inv.created_at', '<=', $toDate)
            ->when($productId, fn($q) => $q->where('i.product_id', $productId))
            ->select('i.product_id', 'p.name as product_name', 'i.unit_price', 'i.quantity')
            ->get();

        $customerItems = DB::table('customer_invoice_items as i')
            ->join('customer_invoices as inv', 'inv.id', '=', 'i.invoice_id')
            ->join('products as p', 'p.id', '=', 'i.product_id')
            ->where('inv.status', 'completed')
            ->whereDate('inv.created_at', '>=', $fromDate)
            ->whereDate('inv.created_at', '<=', $toDate)
            ->when($productId, fn($q) => $q->where('i.product_id', $productId))
            ->select('i.product_id', 'p.name as product_name', 'i.unit_price', 'i.quantity')
            ->get();

        $items = $storeItems->concat($customerItems);

        return [
            'user'         => null,
            'products'     => $this->groupByProduct($items),
            'total_qty'    => $items->sum('quantity'),
            'total_amount' => $items->sum(fn($i) => $i->unit_price * $i->quantity),
        ];
    }

    private function buildStaffData(int $userId, string $fromDate, string $toDate, ?string $productId): array
    {
        $user = User::findOrFail($userId);

        if ($user->role_id === 3) {
            // مسوق → sales_invoice_items
            $items = DB::table('sales_invoice_items as i')
                ->join('sales_invoices as inv', 'inv.id', '=', 'i.invoice_id')
                ->join('products as p', 'p.id', '=', 'i.product_id')
                ->whereIn('inv.status', ['approved', 'pending'])
                ->where('inv.marketer_id', $userId)
                ->whereDate('inv.created_at', '>=', $fromDate)
                ->whereDate('inv.created_at', '<=', $toDate)
                ->when($productId, fn($q) => $q->where('i.product_id', $productId))
                ->select('i.product_id', 'p.name as product_name', 'i.unit_price', 'i.quantity')
                ->get();
        } else {
            // موظف مبيعات → customer_invoice_items
            $items = DB::table('customer_invoice_items as i')
                ->join('customer_invoices as inv', 'inv.id', '=', 'i.invoice_id')
                ->join('products as p', 'p.id', '=', 'i.product_id')
                ->where('inv.status', 'completed')
                ->where('inv.sales_user_id', $userId)
                ->whereDate('inv.created_at', '>=', $fromDate)
                ->whereDate('inv.created_at', '<=', $toDate)
                ->when($productId, fn($q) => $q->where('i.product_id', $productId))
                ->select('i.product_id', 'p.name as product_name', 'i.unit_price', 'i.quantity')
                ->get();
        }

        return [
            'user'     => $user,
            'products' => $this->groupByProduct($items),
            'total_qty'    => $items->sum('quantity'),
            'total_amount' => $items->sum(fn($i) => $i->unit_price * $i->quantity),
        ];
    }

    private function buildCompareData(string $fromDate, string $toDate, ?string $productId, string $sortBy): array
    {
        $allUsers = User::whereIn('role_id', [3, 4])->where('is_active', true)->orderBy('full_name')->get();

        $result = [];

        foreach ($allUsers as $user) {
            if ($user->role_id === 3) {
                $items = DB::table('sales_invoice_items as i')
                    ->join('sales_invoices as inv', 'inv.id', '=', 'i.invoice_id')
                    ->join('products as p', 'p.id', '=', 'i.product_id')
                    ->whereIn('inv.status', ['approved', 'pending'])
                    ->where('inv.marketer_id', $user->id)
                    ->whereDate('inv.created_at', '>=', $fromDate)
                    ->whereDate('inv.created_at', '<=', $toDate)
                    ->when($productId, fn($q) => $q->where('i.product_id', $productId))
                    ->select('i.product_id', 'p.name as product_name', 'i.unit_price', 'i.quantity')
                    ->get();
            } else {
                $items = DB::table('customer_invoice_items as i')
                    ->join('customer_invoices as inv', 'inv.id', '=', 'i.invoice_id')
                    ->join('products as p', 'p.id', '=', 'i.product_id')
                    ->where('inv.status', 'completed')
                    ->where('inv.sales_user_id', $user->id)
                    ->whereDate('inv.created_at', '>=', $fromDate)
                    ->whereDate('inv.created_at', '<=', $toDate)
                    ->when($productId, fn($q) => $q->where('i.product_id', $productId))
                    ->select('i.product_id', 'p.name as product_name', 'i.unit_price', 'i.quantity')
                    ->get();
            }

            if ($items->isEmpty()) continue;

            $totalQty    = $items->sum('quantity');
            $totalAmount = $items->sum(fn($i) => $i->unit_price * $i->quantity);

            $result[] = [
                'user'         => $user,
                'products'     => $this->groupByProduct($items),
                'total_qty'    => $totalQty,
                'total_amount' => round($totalAmount, 2),
            ];
        }

        usort($result, fn($a, $b) =>
            $sortBy === 'qty'
                ? $b['total_qty'] <=> $a['total_qty']
                : $b['total_amount'] <=> $a['total_amount']
        );

        return $result;
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
}
