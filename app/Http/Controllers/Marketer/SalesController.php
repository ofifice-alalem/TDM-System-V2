<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\Store;
use App\Models\Product;
use App\Services\Marketer\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function __construct(private SalesService $service)
    {
        if (!Auth::check()) {
            Auth::loginUsingId(3);
        }
    }

    public function index(Request $request)
    {
        $query = SalesInvoice::with('store', 'items.product')
            ->where('marketer_id', auth()->id());

        $hasFilter = $request->filled('invoice_number') || $request->filled('from_date') || $request->filled('to_date') || $request->filled('store');

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

        if ($request->filled('store')) {
            $storeName = $request->store;
            $query->whereHas('store', function($q) use ($storeName) {
                $q->where('name', 'like', '%' . $storeName . '%');
            });
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        return view('marketer.sales.index', compact('invoices'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        
        $products = Product::with('activePromotion')
            ->where('is_active', true)
            ->leftJoin('marketer_actual_stock', function($join) {
                $join->on('products.id', '=', 'marketer_actual_stock.product_id')
                    ->where('marketer_actual_stock.marketer_id', auth()->id());
            })
            ->select('products.*', 'marketer_actual_stock.quantity as stock')
            ->get();

        return view('marketer.sales.create', compact('stores', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        try {
            $invoice = $this->service->createInvoice(
                auth()->id(),
                $validated['store_id'],
                $validated['items'],
                $validated['notes'] ?? null
            );

            return redirect()->route('marketer.sales.show', $invoice)
                ->with('success', 'تم إنشاء فاتورة البيع بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(SalesInvoice $sale)
    {
        if ($sale->marketer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الفاتورة');
        }
        
        $sale->load('items.product', 'store', 'keeper');
        return view('marketer.sales.show', ['invoice' => $sale]);
    }

    public function cancel(SalesInvoice $sale, Request $request)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:500'
        ]);
        
        try {
            $this->service->cancelInvoice($sale->id, auth()->id());
            $sale->update(['notes' => $validated['notes']]);
            
            return redirect()->route('marketer.sales.index')
                ->with('success', 'تم إلغاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function viewDocumentation(SalesInvoice $sale)
    {
        if ($sale->marketer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الفاتورة');
        }

        if (!$sale->stamped_invoice_image || $sale->status !== 'approved') {
            abort(404);
        }

        $path = storage_path('app/public/' . $sale->stamped_invoice_image);
        
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
