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

    public function index()
    {
        $requests = MarketerRequest::with('items.product')
            ->where('marketer_id', auth()->id())
            ->latest()
            ->paginate(20);

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
        return view('marketer.requests.show', ['request' => $request->load('items.product', 'approver', 'rejecter', 'documenter')]);
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
}
