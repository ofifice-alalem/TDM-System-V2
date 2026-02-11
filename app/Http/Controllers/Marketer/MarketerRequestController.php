<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\MarketerRequest;
use App\Models\Product;
use App\Http\Controllers\Shared\Request\InvoiceController;
use App\Services\Marketer\MarketerRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketerRequestController extends Controller
{
    public function __construct(
        private MarketerRequestService $service,
        private InvoiceController $invoiceController
    ) {
        // Temporary: Auto-login as marketer (ID=3)
        if (!Auth::check()) {
            Auth::loginUsingId(3);
        }
    }

    public function index(Request $request)
    {
        $query = MarketerRequest::with('items.product')
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

        return view('marketer.requests.index', compact('requests'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->leftJoin('main_stock', 'products.id', '=', 'main_stock.product_id')
            ->select('products.*', 'main_stock.quantity as stock')
            ->get();
        return view('marketer.requests.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $marketerRequest = $this->service->createRequest(
            auth()->id(),
            $validated['items'],
            $validated['notes'] ?? null
        );

        return redirect()->route('marketer.requests.show', $marketerRequest)
            ->with('success', 'تم إنشاء الطلب بنجاح');
    }

    public function show(MarketerRequest $request)
    {
        return view('marketer.requests.show', ['request' => $request->load('items.product', 'marketer', 'approver', 'rejecter', 'documenter')]);
    }

    public function cancel(Request $request, MarketerRequest $marketerRequest)
    {
        $validated = $request->validate([
            'notes' => 'required|string'
        ]);

        $this->service->cancelRequest($marketerRequest->id, auth()->id(), $validated['notes']);

        return redirect()->route('marketer.requests.index')
            ->with('success', 'تم إلغاء الطلب بنجاح');
    }

    public function pdf(MarketerRequest $request)
    {
        return $this->invoiceController->generateRequestPdf($request);
    }

    public function viewDocumentation(MarketerRequest $request)
    {
        if (!$request->stamped_image || $request->status !== 'documented') {
            abort(404);
        }

        $path = storage_path('app/public/' . $request->stamped_image);
        
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
