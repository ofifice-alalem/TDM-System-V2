<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return view('admin.staff-pricing.index', compact(
            'marketers', 'salesUsers', 'products',
            'fromDate', 'toDate', 'productId', 'sortBy', 'mode',
            'staffData', 'compareData'
        ));
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
