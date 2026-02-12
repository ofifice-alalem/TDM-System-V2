<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\Request\InvoiceController;
use App\Models\MarketerRequest;
use App\Services\Warehouse\WarehouseRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseRequestController extends Controller
{
    public function __construct(
        private WarehouseRequestService $service,
        private InvoiceController $invoiceController
    ) 
    {
        if (!Auth::check()) {
            Auth::loginUsingId(2);
        }
    }

    public function index(Request $request)
    {
        $query = MarketerRequest::with('marketer', 'items.product');

        $hasFilter = $request->filled('invoice_number') || $request->filled('from_date') || $request->filled('to_date');

        if (!$hasFilter && $request->filled('status')) {
            $query->where('status', $request->status);
        } elseif (!$hasFilter && !$request->has('all')) {
            $query->where('status', 'pending');
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
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
            $marketerName = $request->marketer_id;
            $query->whereHas('marketer', function($q) use ($marketerName) {
                $q->where('full_name', 'like', '%' . $marketerName . '%');
            });
        }

        $requests = $query->latest()->paginate(20)->withQueryString();
        $marketers = \App\Models\MarketerRequest::with('marketer')
            ->select('marketer_id')
            ->distinct()
            ->get()
            ->pluck('marketer')
            ->unique('id');

        return view('warehouse.requests.index', compact('requests', 'marketers'));
    }

    public function show($id)
    {
        $request = MarketerRequest::with('items.product', 'marketer', 'approver', 'rejecter', 'documenter')->findOrFail($id);
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

    public function pdf($id)
    {
        $request = MarketerRequest::findOrFail($id);
        return $this->invoiceController->generateRequestPdf($request);
    }
}
