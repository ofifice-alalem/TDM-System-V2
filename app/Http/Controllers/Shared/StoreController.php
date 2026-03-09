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
        
        $query = Store::query()
            ->with('marketer')
            ->when(auth()->user()->isMarketer(), function($query) {
                $query->where('marketer_id', auth()->id());
            })
            ->when($search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('owner_name', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
            })
            ->withCount('salesInvoices');
        
        $stores = $query->get()->map(function($store) {
            $store->total_debt = $this->calculateDebt($store->id);
            return $store;
        })->sortByDesc('total_debt');

        $stores = new \Illuminate\Pagination\LengthAwarePaginator(
            $stores->forPage($request->get('page', 1), 50),
            $stores->count(),
            50,
            $request->get('page', 1),
            ['path' => $request->url(), 'pageName' => 'page']
        );

        // Filter debt calculations for marketer
        $debtQuery = StoreDebtLedger::query()
            ->when(auth()->user()->isMarketer(), function($query) {
                $query->whereIn('store_id', Store::where('marketer_id', auth()->id())->pluck('id'));
            });

        $totalRemaining = (clone $debtQuery)->whereIn('id', function($query) {
            $query->selectRaw('MAX(id)')
                ->from('store_debt_ledger')
                ->groupBy('store_id');
        })->sum('balance_after');

        $totalDebt = (clone $debtQuery)->where('entry_type', 'sale')->sum('amount');
        $totalPayments = abs((clone $debtQuery)->whereIn('entry_type', ['payment', 'return'])->sum('amount'));

        return view('shared.stores.index', compact('stores', 'search', 'totalDebt', 'totalPayments', 'totalRemaining'));
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
        
        $debt = $this->calculateDebt($store->id);
        
        $stats = [
            'total_sales' => StoreDebtLedger::where('store_id', $store->id)
                ->where('entry_type', 'sale')
                ->sum('amount'),
            'total_returns' => abs(StoreDebtLedger::where('store_id', $store->id)
                ->where('entry_type', 'return')
                ->sum('amount')),
            'total_payments' => abs(StoreDebtLedger::where('store_id', $store->id)
                ->where('entry_type', 'payment')
                ->sum('amount')),
        ];

        return view('shared.stores.show', compact('store', 'debt', 'transactions', 'stats'));
    }

    private function calculateDebt($storeId)
    {
        return StoreDebtLedger::where('store_id', $storeId)
            ->latest('id')
            ->value('balance_after') ?? 0;
    }
}
