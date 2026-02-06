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

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } elseif (!$request->has('all')) {
            $query->where('status', 'pending');
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
        return view('marketer.returns.show', ['request' => $return->load('items.product', 'approver', 'rejecter', 'documenter')]);
    }

    public function cancel(Request $request, MarketerReturnRequest $return)
    {
        $validated = $request->validate([
            'notes' => 'required|string'
        ]);

        $this->service->cancelReturn($return->id, auth()->id(), $validated['notes']);

        return redirect()->route('marketer.returns.index')
            ->with('success', 'تم إلغاء طلب الإرجاع بنجاح');
    }

    public function pdf(MarketerReturnRequest $return)
    {
        return $this->invoiceController->generateReturnPdf($return);
    }
}
