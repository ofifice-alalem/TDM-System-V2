<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Services\Admin\OldCustomerDebtService;
use Illuminate\Http\Request;

class OldCustomerDebtController extends Controller
{
    public function __construct(private OldCustomerDebtService $service) {}

    public function index(Request $request)
    {
        $query = CustomerInvoice::with('customer')
            ->where('sales_user_id', 0)
            ->orderByDesc('id');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('from_date')) {
            try {
                $query->whereDate('created_at', '>=', \Carbon\Carbon::parse($request->from_date)->format('Y-m-d'));
            } catch (\Exception $e) {}
        }

        if ($request->filled('to_date')) {
            try {
                $query->whereDate('created_at', '<=', \Carbon\Carbon::parse($request->to_date)->format('Y-m-d'));
            } catch (\Exception $e) {}
        }

        $debts     = $query->paginate(20)->withQueryString();
        $customers = Customer::orderBy('name')->get();

        return view('admin.old-customer-debts.index', compact('debts', 'customers'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('admin.old-customer-debts.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:0.01',
            'notes'       => 'nullable|string',
        ]);

        try {
            $this->service->create($validated['customer_id'], $validated['amount'], $validated['notes'] ?? null);
            return redirect()->route('admin.old-customer-debts.index')->with('success', 'تم تسجيل الدين السابق بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, CustomerInvoice $oldCustomerDebt)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes'  => 'nullable|string',
        ]);

        try {
            $this->service->update($oldCustomerDebt, $validated['amount'], $validated['notes'] ?? null);
            return redirect()->route('admin.old-customer-debts.index')->with('success', 'تم تعديل الدين بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(CustomerInvoice $oldCustomerDebt)
    {
        try {
            $this->service->delete($oldCustomerDebt);
            return redirect()->route('admin.old-customer-debts.index')->with('success', 'تم حذف الدين بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
