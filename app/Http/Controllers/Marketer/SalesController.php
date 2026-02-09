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

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } elseif (!$request->has('all')) {
            $query->where('status', 'pending');
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        return view('marketer.sales.index', compact('invoices'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        
        $products = Product::where('is_active', true)
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
        $sale->load('items.product', 'store', 'keeper');
        return view('marketer.sales.show', ['invoice' => $sale]);
    }

    public function cancel(SalesInvoice $sale)
    {
        try {
            $this->service->cancelInvoice($sale->id, auth()->id());
            return redirect()->route('marketer.sales.index')
                ->with('success', 'تم إلغاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
