<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsPricingController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate   = $request->input('to_date', now()->format('Y-m-d'));

        $products = $this->buildData($fromDate, $toDate);

        if ($request->has('export_json')) {
            return response()->json($products)
                ->header('Content-Disposition', 'attachment; filename="products_pricing_' . $fromDate . '_' . $toDate . '.json"');
        }

        $grandQty    = array_sum(array_column($products, 'total_qty'));
        $grandAmount = array_sum(array_column($products, 'total_amount'));

        return view('admin.products-pricing.index', compact('products', 'fromDate', 'toDate', 'grandQty', 'grandAmount'));
    }

    private function buildData(string $fromDate, string $toDate): array
    {
        // مبيعات المتاجر
        $storeItems = DB::table('sales_invoice_items as i')
            ->join('sales_invoices as inv', 'inv.id', '=', 'i.invoice_id')
            ->join('products as p', 'p.id', '=', 'i.product_id')
            ->whereIn('inv.status', ['approved', 'pending'])
            ->whereDate('inv.created_at', '>=', $fromDate)
            ->whereDate('inv.created_at', '<=', $toDate)
            ->select('i.product_id', 'p.name as product_name', 'i.unit_price', 'i.quantity')
            ->get();

        // مبيعات العملاء
        $customerItems = DB::table('customer_invoice_items as i')
            ->join('customer_invoices as inv', 'inv.id', '=', 'i.invoice_id')
            ->join('products as p', 'p.id', '=', 'i.product_id')
            ->where('inv.status', 'completed')
            ->whereDate('inv.created_at', '>=', $fromDate)
            ->whereDate('inv.created_at', '<=', $toDate)
            ->select('i.product_id', 'p.name as product_name', 'i.unit_price', 'i.quantity')
            ->get();

        // دمج
        $all = $storeItems->concat($customerItems);

        // تجميع حسب المنتج
        $grouped = $all->groupBy('product_id');

        $products = [];
        foreach ($grouped as $productId => $items) {
            $productName = $items->first()->product_name;

            // تجميع حسب السعر
            $priceGroups = $items->groupBy(fn($i) => number_format((float)$i->unit_price, 2));

            $prices = [];
            foreach ($priceGroups as $price => $priceItems) {
                $prices[] = [
                    'price'       => (float) $price,
                    'times'       => $priceItems->count(),
                    'total_qty'   => $priceItems->sum('quantity'),
                    'total_amount'=> $priceItems->sum(fn($i) => $i->unit_price * $i->quantity),
                ];
            }

            // ترتيب الأسعار تصاعدياً
            usort($prices, fn($a, $b) => $a['price'] <=> $b['price']);

            $totalQty    = array_sum(array_column($prices, 'total_qty'));
            $totalAmount = array_sum(array_column($prices, 'total_amount'));
            $totalTimes  = array_sum(array_column($prices, 'times'));
            $avgPrice    = $totalQty > 0 ? $totalAmount / $totalQty : 0;

            $products[] = [
                'product_id'   => $productId,
                'product_name' => $productName,
                'prices'       => $prices,
                'total_times'  => $totalTimes,
                'total_qty'    => $totalQty,
                'avg_price'    => round($avgPrice, 2),
                'total_amount' => round($totalAmount, 2),
            ];
        }

        // ترتيب حسب إجمالي المبلغ تنازلياً
        usort($products, fn($a, $b) => $b['total_amount'] <=> $a['total_amount']);

        return $products;
    }
}
