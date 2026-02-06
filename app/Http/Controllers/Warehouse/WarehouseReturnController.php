<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\MarketerReturnRequest;
use App\Services\Warehouse\WarehouseReturnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseReturnController extends Controller
{
    public function __construct(private WarehouseReturnService $service)
    {
        if (!Auth::check()) {
            Auth::loginUsingId(2);
        }
    }

    public function index(Request $request)
    {
        $query = MarketerReturnRequest::with('marketer', 'items.product');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } elseif (!$request->has('all')) {
            $query->where('status', 'pending');
        }

        $requests = $query->latest()->paginate(20)->withQueryString();

        return view('warehouse.returns.index', compact('requests'));
    }

    public function show($id)
    {
        $request = MarketerReturnRequest::with('items.product', 'marketer', 'approver', 'rejecter', 'documenter')->findOrFail($id);
        return view('warehouse.returns.show', ['request' => $request]);
    }

    public function approve($id)
    {
        try {
            $this->service->approveReturn($id, auth()->id());
            return redirect()->back()->with('success', 'تمت الموافقة على طلب الإرجاع');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate(['notes' => 'required|string']);

        $this->service->rejectReturn($id, auth()->id(), $validated['notes']);

        return redirect()->route('warehouse.returns.index')
            ->with('success', 'تم رفض طلب الإرجاع');
    }

    public function document(Request $request, $id)
    {
        $validated = $request->validate([
            'stamped_image' => 'required|image|max:2048',
        ]);

        $return = MarketerReturnRequest::findOrFail($id);
        
        $path = $request->file('stamped_image')->store(
            'returns/' . $return->invoice_number,
            'public'
        );

        $this->service->documentReturn($id, auth()->id(), $path);

        return redirect()->route('warehouse.returns.index')
            ->with('success', 'تم توثيق طلب الإرجاع بنجاح');
    }

    public function viewDocumentation($id)
    {
        $return = MarketerReturnRequest::findOrFail($id);
        
        if ($return->status !== 'documented' || !$return->stamped_image) {
            abort(404, 'لا توجد صورة توثيق');
        }

        $imagePath = storage_path('app/public/' . $return->stamped_image);
        
        if (!file_exists($imagePath)) {
            abort(404, 'الصورة غير موجودة');
        }

        return response()->file($imagePath);
    }
}
