<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use App\Services\Warehouse\WarehouseSalesReturnService;
use Illuminate\Http\Request;

class WarehouseSalesReturnController extends Controller
{
    protected $salesReturnService;

    public function __construct(WarehouseSalesReturnService $salesReturnService)
    {
        $this->salesReturnService = $salesReturnService;
    }

    public function index(Request $request)
    {
        $query = SalesReturn::with(['store', 'marketer', 'salesInvoice', 'items.product'])
            ->latest();

        $hasFilter = $request->filled('return_number') || $request->filled('from_date') || $request->filled('to_date') || $request->filled('marketer_id') || $request->filled('store_id');

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

        if ($request->filled('marketer_id')) {
            $query->where('marketer_id', $request->marketer_id);
        }

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $returns   = $query->paginate(10)->withQueryString();
        $marketers = \App\Models\User::where('role_id', 3)->where('is_active', true)->get();
        $stores    = \App\Models\Store::orderBy('name')->get(['id', 'name']);

        return view('warehouse.sales-returns.index', compact('returns', 'marketers', 'stores'));
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['store', 'marketer', 'salesInvoice', 'items.product', 'items.salesInvoiceItem', 'keeper']);
        return view('warehouse.sales-returns.show', compact('salesReturn'));
    }

    public function approve(Request $request, $id)
    {
        $salesReturn = SalesReturn::findOrFail($id);
        $keeperId = auth()->id();

        try {
            $stampedImage = null;
            if ($request->hasFile('stamped_image')) {
                $stampedImage = $request->file('stamped_image')->store(
                    'sales-returns/' . $salesReturn->return_number,
                    'public'
                );
            }
            
            $this->salesReturnService->approveReturn($salesReturn, $keeperId, $stampedImage);
            return redirect()->route('warehouse.sales-returns.show', $salesReturn)
                ->with('success', 'تم الموافقة على طلب الإرجاع وتوثيقه بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $salesReturn = SalesReturn::findOrFail($id);
        $keeperId = auth()->id();

        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        try {
            $this->salesReturnService->rejectReturn($salesReturn, $keeperId, $request->notes);
            return redirect()->route('warehouse.sales-returns.index')
                ->with('success', 'تم رفض طلب الإرجاع');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function viewDocumentation($id)
    {
        $salesReturn = SalesReturn::findOrFail($id);
        
        if ($salesReturn->status !== 'approved' || !$salesReturn->stamped_image) {
            abort(404, 'لا توجد صورة توثيق');
        }

        $imagePath = storage_path('app/public/' . $salesReturn->stamped_image);
        
        if (!file_exists($imagePath)) {
            abort(404, 'الصورة غير موجودة');
        }

        return response()->file($imagePath);
    }
}
