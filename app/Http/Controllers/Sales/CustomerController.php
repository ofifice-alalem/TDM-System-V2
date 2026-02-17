<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('invoices')
            ->withSum('debtLedger', 'amount');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // Sort filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'debt_asc':
                    $query->orderBy('debt_ledger_sum_amount', 'asc');
                    break;
                case 'debt_desc':
                    $query->orderBy('debt_ledger_sum_amount', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $customers = $query->paginate(20)->withQueryString();

        return view('sales.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('sales.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }

        return redirect()->route('sales.customers.show', $customer->id)
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    public function show(Customer $customer)
    {
        $customer->load(['invoices' => function($q) {
            $q->latest()->take(10);
        }, 'debtLedger' => function($q) {
            $q->latest()->take(20);
        }, 'returns']);

        $totalDebt = $customer->debtLedger()->sum('amount');
        $totalInvoices = $customer->invoices()->sum('total_amount');
        $totalPayments = $customer->payments()->sum('amount');
        $totalReturns = $customer->returns()->sum('total_amount');

        return view('sales.customers.show', compact('customer', 'totalDebt', 'totalInvoices', 'totalPayments', 'totalReturns'));
    }

    public function edit(Customer $customer)
    {
        return view('sales.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'id_number' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $customer->update($validated);

        return redirect()->route('sales.customers.show', $customer->id)
            ->with('success', 'تم تحديث بيانات العميل بنجاح');
    }
}
