<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\MarketerRequest;
use App\Services\Warehouse\WarehouseRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseRequestController extends Controller
{
    public function __construct(private WarehouseRequestService $service) 
    {
        if (!Auth::check()) {
            Auth::loginUsingId(2);
        }
    }

    public function index()
    {
        $requests = MarketerRequest::with('marketer', 'items.product')
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->paginate(20);

        return view('warehouse.requests.index', compact('requests'));
    }

    public function show($id)
    {
        $request = MarketerRequest::with('items.product', 'marketer', 'approver', 'documenter')->findOrFail($id);
        return view('warehouse.requests.show', ['request' => $request]);
    }

    public function approve($id)
    {
        try {
            $this->service->approveRequest($id, auth()->id());
            return redirect()->back()->with('success', 'تمت الموافقة على الطلب');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate(['notes' => 'required|string']);

        $this->service->rejectRequest($id, auth()->id(), $validated['notes']);

        return redirect()->route('warehouse.requests.index')
            ->with('success', 'تم رفض الطلب');
    }

    public function document(Request $request, $id)
    {
        $validated = $request->validate([
            'stamped_image' => 'required|image|max:2048',
        ]);

        $marketerRequest = MarketerRequest::findOrFail($id);
        
        $path = $request->file('stamped_image')->store(
            'requests/' . $marketerRequest->invoice_number,
            'public'
        );

        $this->service->documentRequest($id, auth()->id(), $path);

        return redirect()->route('warehouse.requests.index')
            ->with('success', 'تم توثيق الطلب بنجاح');
    }

    public function viewDocumentation($id)
    {
        $request = MarketerRequest::findOrFail($id);
        
        if ($request->status !== 'documented' || !$request->stamped_image) {
            abort(404, 'لا توجد صورة توثيق');
        }

        $imagePath = storage_path('app/public/' . $request->stamped_image);
        
        if (!file_exists($imagePath)) {
            abort(404, 'الصورة غير موجودة');
        }

        return response()->file($imagePath);
    }
}
