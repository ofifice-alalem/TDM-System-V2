<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use App\Models\StorePayment;
use App\Models\StoreDebtLedger;
use App\Models\User;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function create()
    {
        $marketers = User::where('role_id', 3)->where('is_active', true)->get();
        return view('shared.stores.create', compact('marketers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'owner_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:200',
            'address' => 'nullable|string',
            'marketer_id' => 'nullable|exists:users,id',
        ]);

        Store::create($validated);

        return redirect()->route(request()->routeIs('admin.*') ? 'admin.stores.index' : 'warehouse.stores.index')
            ->with('success', 'تم إضافة المتجر بنجاح');
    }

    public function edit(Store $store)
    {
        $marketers = User::where('role_id', 3)->where('is_active', true)->get();
        return view('shared.stores.edit', compact('store', 'marketers'));
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'owner_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:200',
            'address' => 'nullable|string',
            'marketer_id' => 'nullable|exists:users,id',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $store->update($validated);

        return redirect()->route(request()->routeIs('admin.*') ? 'admin.stores.index' : 'warehouse.stores.index')
            ->with('success', 'تم تحديث بيانات المتجر بنجاح');
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $marketerId = $request->get('marketer_id');
        
        $query = Store::query()
            ->with('marketer')
            ->when(auth()->user()->isMarketer(), function($query) {
                $query->where('marketer_id', auth()->id());
            })
            ->when($marketerId && !auth()->user()->isMarketer(), function($query) use ($marketerId) {
                $query->where('marketer_id', $marketerId);
            })
            ->when($search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('owner_name', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
            })
            ->withCount('salesInvoices');
        
        $storeIds = $query->pluck('id');

        $pendingSales = SalesInvoice::whereIn('store_id', $storeIds)->where('status', 'pending')
            ->selectRaw('store_id, SUM(total_amount) as total')->groupBy('store_id')
            ->pluck('total', 'store_id');

        $pendingPayments = StorePayment::whereIn('store_id', $storeIds)->where('status', 'pending')
            ->selectRaw('store_id, SUM(amount) as total')->groupBy('store_id')
            ->pluck('total', 'store_id');

        $pendingReturns = SalesReturn::whereIn('store_id', $storeIds)->where('status', 'pending')
            ->selectRaw('store_id, SUM(total_amount) as total')->groupBy('store_id')
            ->pluck('total', 'store_id');

        $ledgerSales = StoreDebtLedger::whereIn('store_id', $storeIds)->where('entry_type', 'sale')
            ->selectRaw('store_id, SUM(amount) as total')->groupBy('store_id')->pluck('total', 'store_id');
        $ledgerPayments = StoreDebtLedger::whereIn('store_id', $storeIds)->where('entry_type', 'payment')
            ->selectRaw('store_id, ABS(SUM(amount)) as total')->groupBy('store_id')->pluck('total', 'store_id');
        $ledgerReturns = StoreDebtLedger::whereIn('store_id', $storeIds)->where('entry_type', 'return')
            ->selectRaw('store_id, ABS(SUM(amount)) as total')->groupBy('store_id')->pluck('total', 'store_id');

        $stores = $query->get()->map(function($store) use ($pendingSales, $pendingPayments, $pendingReturns, $ledgerSales, $ledgerPayments, $ledgerReturns) {
            $confirmed = ($ledgerSales[$store->id] ?? 0) - ($ledgerPayments[$store->id] ?? 0) - ($ledgerReturns[$store->id] ?? 0);
            $pending   = ($pendingSales[$store->id] ?? 0) - ($pendingPayments[$store->id] ?? 0) - ($pendingReturns[$store->id] ?? 0);
            $store->confirmed_debt = $confirmed;
            $store->pending_net    = $pending;
            $store->total_debt     = $confirmed + $pending;
            return $store;
        })->sortByDesc('total_debt')->values();

        $stores = new \Illuminate\Pagination\LengthAwarePaginator(
            $stores->forPage($request->get('page', 1), 50),
            $stores->count(),
            50,
            $request->get('page', 1),
            ['path' => $request->url(), 'pageName' => 'page']
        );

        // بيانات البحث السريع - كل المتاجر بدون pagination
        $allStoresForSearch = Store::query()
            ->when(auth()->user()->isMarketer(), function($query) {
                $query->where('marketer_id', auth()->id());
            })
            ->when($marketerId && !auth()->user()->isMarketer(), function($query) use ($marketerId) {
                $query->where('marketer_id', $marketerId);
            })
            ->select('id', 'name', 'owner_name', 'location')
            ->get();

        $totalPendingSales    = SalesInvoice::whereIn('store_id', $storeIds)->where('status', 'pending')->sum('total_amount');
        $totalPendingPayments = StorePayment::whereIn('store_id', $storeIds)->where('status', 'pending')->sum('amount');
        $totalPendingReturns  = SalesReturn::whereIn('store_id', $storeIds)->where('status', 'pending')->sum('total_amount');

        $totalDebt     = SalesInvoice::whereIn('store_id', $storeIds)->whereIn('status', ['approved', 'pending'])->where('marketer_id', '!=', 0)->sum('total_amount');
        $totalPayments  = abs(StoreDebtLedger::whereIn('store_id', $storeIds)->where('entry_type', 'payment')->sum('amount'))
                        + abs(StoreDebtLedger::whereIn('store_id', $storeIds)->where('entry_type', 'return')->sum('amount'))
                        + $totalPendingPayments + $totalPendingReturns;
        $totalOldDebt   = SalesInvoice::whereIn('store_id', $storeIds)->where('marketer_id', 0)->sum('total_amount');
        $totalRemaining = $totalDebt + $totalOldDebt - $totalPayments;

        return view('shared.stores.index', compact('stores', 'search', 'totalDebt', 'totalPayments', 'totalRemaining', 'totalOldDebt', 'allStoresForSearch'));
    }

    public function show(Store $store)
    {
        // Get last 15 transactions (sales invoices, returns, payments)
        $transactions = collect();

        // Sales Invoices (approved only)
        $salesInvoices = SalesInvoice::where('store_id', $store->id)
            ->where('status', 'approved')
            ->latest('confirmed_at')
            ->take(15)
            ->get()
            ->map(function($invoice) {
                return [
                    'type' => 'sale',
                    'id' => $invoice->id,
                    'number' => $invoice->invoice_number,
                    'amount' => $invoice->total_amount,
                    'marketer' => $invoice->marketer->full_name ?? 'مسوق',
                    'date' => $invoice->confirmed_at,
                ];
            });

        // Sales Returns (approved only)
        $salesReturns = SalesReturn::where('store_id', $store->id)
            ->where('status', 'approved')
            ->latest('confirmed_at')
            ->take(15)
            ->get()
            ->map(function($return) {
                return [
                    'type' => 'return',
                    'id' => $return->id,
                    'number' => $return->return_number,
                    'amount' => $return->total_amount,
                    'marketer' => $return->marketer->full_name ?? 'مسوق',
                    'date' => $return->confirmed_at,
                ];
            });

        // Store Payments (approved only)
        $storePayments = StorePayment::where('store_id', $store->id)
            ->where('status', 'approved')
            ->latest('confirmed_at')
            ->take(15)
            ->get()
            ->map(function($payment) {
                return [
                    'type' => 'payment',
                    'id' => $payment->id,
                    'number' => $payment->payment_number,
                    'amount' => $payment->amount,
                    'marketer' => $payment->marketer->full_name ?? 'مسوق',
                    'date' => $payment->confirmed_at,
                ];
            });

        // Merge and sort by date
        $transactions = $transactions
            ->merge($salesInvoices)
            ->merge($salesReturns)
            ->merge($storePayments)
            ->sortByDesc('date')
            ->take(15);
        
        $pendingSales    = SalesInvoice::where('store_id', $store->id)->where('status', 'pending')->sum('total_amount');
        $pendingPayments = StorePayment::where('store_id', $store->id)->where('status', 'pending')->sum('amount');
        $pendingReturns  = SalesReturn::where('store_id', $store->id)->where('status', 'pending')->sum('total_amount');

        $ledgerSales    = StoreDebtLedger::where('store_id', $store->id)->where('entry_type', 'sale')->sum('amount');
        $ledgerPayments = abs(StoreDebtLedger::where('store_id', $store->id)->where('entry_type', 'payment')->sum('amount'));
        $ledgerReturns  = abs(StoreDebtLedger::where('store_id', $store->id)->where('entry_type', 'return')->sum('amount'));

        $stats = [
            'total_sales'      => $ledgerSales + $pendingSales,
            'total_payments'   => $ledgerPayments + $pendingPayments,
            'total_returns'    => $ledgerReturns + $pendingReturns,
            'pending_sales'    => $pendingSales,
            'pending_payments' => $pendingPayments,
            'pending_returns'  => $pendingReturns,
        ];

        $confirmedDebt = $ledgerSales - $ledgerPayments - $ledgerReturns;
        $debt = $confirmedDebt + $pendingSales - $pendingPayments - $pendingReturns;

        return view('shared.stores.show', compact('store', 'debt', 'confirmedDebt', 'transactions', 'stats'));
    }

    private function calculateDebt($storeId)
    {
        $ledgerSales    = StoreDebtLedger::where('store_id', $storeId)->where('entry_type', 'sale')->sum('amount');
        $ledgerPayments = abs(StoreDebtLedger::where('store_id', $storeId)->where('entry_type', 'payment')->sum('amount'));
        $ledgerReturns  = abs(StoreDebtLedger::where('store_id', $storeId)->where('entry_type', 'return')->sum('amount'));

        $pendingSales    = SalesInvoice::where('store_id', $storeId)->where('status', 'pending')->sum('total_amount');
        $pendingPayments = StorePayment::where('store_id', $storeId)->where('status', 'pending')->sum('amount');
        $pendingReturns  = SalesReturn::where('store_id', $storeId)->where('status', 'pending')->sum('total_amount');

        return ($ledgerSales - $ledgerPayments - $ledgerReturns) + $pendingSales - $pendingPayments - $pendingReturns;
    }
}
