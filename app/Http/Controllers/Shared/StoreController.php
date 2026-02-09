<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use App\Models\StorePayment;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $stores = Store::query()
            ->when($search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('owner_name', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
            })
            ->withCount('salesInvoices')
            ->get()
            ->map(function($store) {
                $store->total_debt = $this->calculateDebt($store->id);
                return $store;
            });

        return view('shared.stores.index', compact('stores', 'search'));
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
            'total_sales' => SalesInvoice::where('store_id', $store->id)
                ->where('status', 'approved')
                ->sum('total_amount'),
            'total_returns' => SalesReturn::where('store_id', $store->id)
                ->where('status', 'approved')
                ->sum('total_amount'),
            'total_payments' => StorePayment::where('store_id', $store->id)
                ->where('status', 'approved')
                ->sum('amount'),
        ];

        return view('shared.stores.show', compact('store', 'debt', 'transactions', 'stats'));
    }

    private function calculateDebt($storeId)
    {
        $sales = SalesInvoice::where('store_id', $storeId)
            ->where('status', 'approved')
            ->sum('total_amount');
        
        $returns = SalesReturn::where('store_id', $storeId)
            ->where('status', 'approved')
            ->sum('total_amount');
        
        $payments = StorePayment::where('store_id', $storeId)
            ->where('status', 'approved')
            ->sum('amount');
        
        return $sales - $returns - $payments;
    }
}
