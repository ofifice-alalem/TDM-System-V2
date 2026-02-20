<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\FactoryInvoice;
use App\Models\Product;
use App\Services\Warehouse\MainStockService;
use Illuminate\Http\Request;

class FactoryInvoiceController extends Controller
{
    public function __construct(private MainStockService $service) {}

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('shared.factory-invoices.create', compact('products'));
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

        $routePrefix = request()->routeIs('admin.*') ? 'admin' : 'warehouse';
        return redirect()->route($routePrefix . '.factory-invoices.show', $invoice)
            ->with('success', 'تم إنشاء فاتورة المصنع بنجاح');
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

        $routePrefix = request()->routeIs('admin.*') ? 'admin' : 'warehouse';
        return redirect()->route($routePrefix . '.factory-invoices.index')
            ->with('success', 'تم توثيق الفاتورة وإضافة الكميات للمخزن');
    }

    public function cancel(Request $request, FactoryInvoice $factoryInvoice)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $this->service->cancelInvoice($factoryInvoice->id, auth()->id(), $validated['cancellation_reason']);

        $routePrefix = request()->routeIs('admin.*') ? 'admin' : 'warehouse';
        return redirect()->route($routePrefix . '.factory-invoices.index')
            ->with('success', 'تم إلغاء الفاتورة بنجاح');
    }
}
