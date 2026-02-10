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

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if (!$request->has('status') && !$request->has('all')) {
            $query->where('status', 'pending');
        }

        $returns = $query->paginate(10);

        return view('warehouse.sales-returns.index', compact('returns'));
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['store', 'marketer', 'salesInvoice', 'items.product', 'items.salesInvoiceItem', 'keeper']);
        return view('warehouse.sales-returns.show', compact('salesReturn'));
    }

    public function approve(Request $request, $id)
    {
        $salesReturn = SalesReturn::findOrFail($id);
        $keeperId = 1; // Temporary

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

        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        try {
            $this->salesReturnService->rejectReturn($salesReturn, $request->notes);
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
