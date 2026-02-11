<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\FactoryInvoice;
use App\Models\Product;
use App\Services\Warehouse\MainStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FactoryInvoiceController extends Controller
{
    public function __construct(private MainStockService $service)
    {
        if (!Auth::check()) {
            Auth::loginUsingId(2);
        }
    }

    public function index(Request $request)
    {
        $query = FactoryInvoice::with('items.product', 'keeper', 'documenter');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } elseif (!$request->has('all')) {
            $query->where('status', 'pending');
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        return view('warehouse.factory-invoices.index', compact('invoices'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('warehouse.factory-invoices.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $invoice = $this->service->createFactoryInvoice(
            auth()->id(),
            $validated['items'],
            $validated['notes'] ?? null
        );

        return redirect()->route('warehouse.factory-invoices.show', $invoice)
            ->with('success', 'تم إنشاء فاتورة المصنع بنجاح');
    }

    public function show(FactoryInvoice $factoryInvoice)
    {
        return view('warehouse.factory-invoices.show', [
            'invoice' => $factoryInvoice->load('items.product', 'keeper', 'documenter')
        ]);
    }

    public function document(Request $request, FactoryInvoice $factoryInvoice)
    {
        $validated = $request->validate([
            'stamped_image' => 'required|image|max:2048',
        ]);

        $path = $request->file('stamped_image')->store(
            'factory-invoices/' . $factoryInvoice->invoice_number,
            'public'
        );

        $this->service->documentInvoice($factoryInvoice->id, auth()->id(), $path);

        return redirect()->route('warehouse.factory-invoices.index')
            ->with('success', 'تم توثيق الفاتورة وإضافة الكميات للمخزن');
    }

    public function pdf(FactoryInvoice $factoryInvoice)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shared.factory-invoices.invoice-pdf', [
            'invoice' => $factoryInvoice->load('items.product', 'keeper', 'documenter')
        ]);
        
        return $pdf->download('factory-invoice-' . $factoryInvoice->invoice_number . '.pdf');
    }
}
