<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\Store;
use App\Services\Admin\OldDebtService;
use Illuminate\Http\Request;

class OldDebtController extends Controller
{
    public function __construct(private OldDebtService $service) {}

    public function index(Request $request)
    {
        $query = SalesInvoice::with('store')
            ->where('marketer_id', 0)
            ->orderByDesc('id');

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
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

        $debts  = $query->paginate(20)->withQueryString();
        $stores = Store::orderBy('name')->get();

        return view('admin.old-debts.index', compact('debts', 'stores'));
    }

    public function create()
    {
        $stores = Store::orderBy('name')->get();
        return view('admin.old-debts.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'amount'   => 'required|numeric|min:0.01',
            'notes'    => 'nullable|string',
        ]);

        try {
            $this->service->create($validated['store_id'], $validated['amount'], $validated['notes'] ?? null);
            return redirect()->route('admin.old-debts.index')->with('success', 'تم تسجيل الدين السابق بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, SalesInvoice $oldDebt)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes'  => 'nullable|string',
        ]);

        try {
            $this->service->update($oldDebt, $validated['amount'], $validated['notes'] ?? null);
            return redirect()->route('admin.old-debts.index')->with('success', 'تم تعديل الدين بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(SalesInvoice $oldDebt)
    {
        try {
            $this->service->delete($oldDebt);
            return redirect()->route('admin.old-debts.index')->with('success', 'تم حذف الدين بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
