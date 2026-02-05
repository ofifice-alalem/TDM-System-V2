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
        // Temporary: Auto-login as warehouse keeper (ID=2)
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

    public function show(MarketerRequest $request)
    {
        return view('warehouse.requests.show', ['request' => $request->load('items.product', 'marketer')]);
    }

    public function approve(MarketerRequest $request)
    {
        try {
            $this->service->approveRequest($request->id, auth()->id());
            return redirect()->back()->with('success', 'تمت الموافقة على الطلب');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, MarketerRequest $marketerRequest)
    {
        $validated = $request->validate(['notes' => 'required|string']);

        $this->service->rejectRequest($marketerRequest->id, auth()->id(), $validated['notes']);

        return redirect()->route('warehouse.requests.index')
            ->with('success', 'تم رفض الطلب');
    }

    public function document(Request $request, MarketerRequest $marketerRequest)
    {
        $validated = $request->validate([
            'stamped_image' => 'required|image|max:2048',
        ]);

        $path = $request->file('stamped_image')->store('requests', 'public');

        $this->service->documentRequest($marketerRequest->id, auth()->id(), $path);

        return redirect()->route('warehouse.requests.index')
            ->with('success', 'تم توثيق الطلب بنجاح');
    }
}
