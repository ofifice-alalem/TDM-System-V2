<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\Sales\CustomerInvoiceController as SharedCustomerInvoiceController;
use App\Models\CustomerInvoice;
use App\Models\Customer;
use App\Models\Product;
use App\Services\Sales\CustomerInvoiceService;
use Illuminate\Http\Request;

class CustomerInvoiceController extends Controller
{
    public function __construct(
        private CustomerInvoiceService $invoiceService,
        private SharedCustomerInvoiceController $pdfController
    )
    {
    }

    public function index(Request $request)
    {
        $query = CustomerInvoice::with(['customer', 'salesUser']);

        // Filter by invoice number
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        // Filter by customer
        if ($request->filled('customer')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer . '%');
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by amount range
        if ($request->filled('amount_from')) {
            $query->where('total_amount', '>=', $request->amount_from);
        }
        if ($request->filled('amount_to')) {
            $query->where('total_amount', '<=', $request->amount_to);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('sales.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->with('mainStock')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => $p->customer_price ?? $p->current_price,
                    'stock' => $p->mainStock->quantity ?? 0
                ];
            });

        return view('sales.invoices.create', compact('products'));
    }

    public function searchCustomers(Request $request)
    {
        $query = $request->get('query');
        
        $customers = Customer::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('phone', 'like', '%' . $query . '%');
            })
            ->limit(20)
            ->get(['id', 'name', 'phone']);

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,transfer,check',
            'notes' => 'nullable|string',
        ]);

        try {
            $invoice = $this->invoiceService->createInvoice(
                auth()->id(),
                $validated['customer_id'],
                $validated['items'],
                $validated['discount_amount'] ?? 0,
                $validated['paid_amount'] ?? 0,
                $validated['payment_method'] ?? 'cash',
                $validated['notes'] ?? null
            );

            return redirect()->route('sales.invoices.show', $invoice->id)
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(CustomerInvoice $invoice)
    {
        $invoice->load(['items.product', 'customer', 'salesUser', 'returns']);
        return view('sales.invoices.show', compact('invoice'));
    }

    public function cancel(CustomerInvoice $invoice)
    {
        try {
            $this->invoiceService->cancelInvoice($invoice->id, auth()->id());
            return redirect()->route('sales.invoices.show', $invoice->id)
                ->with('success', 'تم إلغاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pdf(CustomerInvoice $invoice)
    {
        return $this->pdfController->generateInvoicePdf($invoice);
    }
}
