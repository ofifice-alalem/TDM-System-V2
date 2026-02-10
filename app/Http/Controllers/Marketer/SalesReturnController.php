<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use App\Models\SalesInvoice;
use App\Services\Marketer\SalesReturnService;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    protected $salesReturnService;

    public function __construct(SalesReturnService $salesReturnService)
    {
        $this->salesReturnService = $salesReturnService;
    }

    public function index(Request $request)
    {
        $marketerId = 3; // Temporary
        
        $query = SalesReturn::where('marketer_id', $marketerId)
            ->with(['store', 'salesInvoice', 'items.product'])
            ->latest();

        $hasFilter = $request->filled('return_number') || $request->filled('from_date') || $request->filled('to_date') || $request->filled('store');

        if (!$hasFilter && $request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if (!$hasFilter && !$request->has('status') && !$request->has('all')) {
            $query->where('status', 'pending');
        }

        if ($request->filled('return_number')) {
            $query->where('return_number', 'like', '%' . $request->return_number . '%');
        }

        if ($request->filled('from_date')) {
            try {
                $fromDate = \Carbon\Carbon::parse($request->from_date)->format('Y-m-d');
                $query->whereDate('created_at', '>=', $fromDate);
            } catch (\Exception $e) {}
        }

        if ($request->filled('to_date')) {
            try {
                $toDate = \Carbon\Carbon::parse($request->to_date)->format('Y-m-d');
                $query->whereDate('created_at', '<=', $toDate);
            } catch (\Exception $e) {}
        }

        if ($request->filled('store')) {
            $storeName = $request->store;
            $query->whereHas('store', function($q) use ($storeName) {
                $q->where('name', 'like', '%' . $storeName . '%');
            });
        }

        $returns = $query->paginate(10);

        return view('marketer.sales-returns.index', compact('returns'));
    }

    public function create()
    {
        $marketerId = 3; // Temporary
        
        $approvedInvoices = SalesInvoice::where('marketer_id', $marketerId)
            ->where('status', 'approved')
            ->with(['store', 'items.product'])
            ->latest()
            ->get();

        return view('marketer.sales-returns.create', compact('approvedInvoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_invoice_id' => 'required|exists:sales_invoices,id',
            'items' => 'required|array|min:1',
            'items.*.sales_invoice_item_id' => 'required|exists:sales_invoice_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $marketerId = 3; // Temporary
        $keeperId = 1; // Temporary

        try {
            $return = $this->salesReturnService->createReturn($marketerId, $validated);
            return redirect()->route('marketer.sales-returns.show', $return)
                ->with('success', 'تم إنشاء طلب الإرجاع بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['store', 'salesInvoice', 'items.product', 'items.salesInvoiceItem', 'marketer', 'keeper']);
        return view('marketer.sales-returns.show', compact('salesReturn'));
    }

    public function cancel(Request $request, SalesReturn $salesReturn)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        try {
            $this->salesReturnService->cancelReturn($salesReturn, $request->notes);
            return redirect()->route('marketer.sales-returns.index')
                ->with('success', 'تم إلغاء طلب الإرجاع بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
