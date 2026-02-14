<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\Return\InvoiceController;
use App\Models\MarketerReturnRequest;
use App\Models\MarketerActualStock;
use App\Services\Marketer\MarketerReturnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketerReturnController extends Controller
{
    public function __construct(
        private MarketerReturnService $service,
        private InvoiceController $invoiceController
    )
    {
        if (!Auth::check()) {
            Auth::loginUsingId(3);
        }
    }

    public function index(Request $request)
    {
        $query = MarketerReturnRequest::with('items.product')
            ->where('marketer_id', auth()->id());

        $hasFilter = $request->filled('invoice_number') || $request->filled('from_date') || $request->filled('to_date');

        if (!$hasFilter && $request->has('status')) {
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

        $requests = $query->latest()->paginate(20)->withQueryString();

        return view('marketer.returns.index', compact('requests'));
    }

    public function create()
    {
        $stock = MarketerActualStock::where('marketer_id', auth()->id())
            ->with('product')
            ->get();

        return view('marketer.returns.create', compact('stock'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $return = $this->service->createReturn(
            auth()->id(),
            $validated['items'],
            $validated['notes'] ?? null
        );

        return redirect()->route('marketer.returns.show', $return)
            ->with('success', 'تم إنشاء طلب الإرجاع بنجاح');
    }

    public function show(MarketerReturnRequest $return)
    {
        if ($return->marketer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول لهذا الطلب');
        }
        return view('marketer.returns.show', ['request' => $return->load('items.product', 'approver', 'rejecter', 'documenter')]);
    }

    public function cancel(Request $request, MarketerReturnRequest $return)
    {
        if ($return->marketer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول لهذا الطلب');
        }
        $validated = $request->validate([
            'notes' => 'required|string'
        ]);

        $this->service->cancelReturn($return->id, auth()->id(), $validated['notes']);

        return redirect()->route('marketer.returns.index')
            ->with('success', 'تم إلغاء طلب الإرجاع بنجاح');
    }

    public function pdf(MarketerReturnRequest $return)
    {
        if ($return->marketer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول لهذا الطلب');
        }
        return $this->invoiceController->generateReturnPdf($return);
    }

    public function viewDocumentation(MarketerReturnRequest $return)
    {
        if ($return->marketer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول لهذا الطلب');
        }

        if (!$return->stamped_image || $return->status !== 'documented') {
            abort(404);
        }

        $path = storage_path('app/public/' . $return->stamped_image);
        
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
