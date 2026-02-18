<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\Sales\CustomerReturnController as SharedCustomerReturnController;
use App\Models\CustomerReturn;
use App\Models\CustomerInvoice;
use App\Services\Sales\CustomerReturnService;
use Illuminate\Http\Request;

class CustomerReturnController extends Controller
{
    public function __construct(
        private CustomerReturnService $returnService,
        private SharedCustomerReturnController $pdfController
    )
    {
    }

    public function index()
    {
        $returns = CustomerReturn::with(['customer', 'invoice', 'salesUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('sales.returns.index', compact('returns'));
    }

    public function create()
    {
        return view('sales.returns.create');
    }

    public function searchInvoices(Request $request)
    {
        $query = $request->get('q');
        
        $invoices = CustomerInvoice::with('customer')
            ->where('status', 'completed')
            ->where(function($q) use ($query) {
                $q->where('invoice_number', 'like', '%' . $query . '%')
                  ->orWhereHas('customer', function($q) use ($query) {
                      $q->where('name', 'like', '%' . $query . '%');
                  });
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'invoices' => $invoices->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_id' => $invoice->customer_id,
                    'customer_name' => $invoice->customer->name,
                    'total_amount' => $invoice->total_amount,
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:customer_invoices,id',
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.invoice_item_id' => 'required|exists:customer_invoice_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        try {
            $return = $this->returnService->createReturn(
                auth()->id(),
                $validated['invoice_id'],
                $validated['customer_id'],
                $validated['items'],
                $validated['notes'] ?? null
            );

            return redirect()->route('sales.returns.show', $return->id)
                ->with('success', 'تم إنشاء المرتجع بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(CustomerReturn $return)
    {
        $return->load(['items.product', 'invoice', 'customer', 'salesUser']);
        return view('sales.returns.show', compact('return'));
    }

    public function getInvoiceItems($invoiceId)
    {
        $invoice = CustomerInvoice::with('items.product')->findOrFail($invoiceId);
        
        // Get all previous returns for this invoice
        $previousReturns = CustomerReturn::where('invoice_id', $invoiceId)
            ->where('status', '!=', 'cancelled')
            ->with('items')
            ->get();
        
        // Calculate returned quantities per item
        $returnedQuantities = [];
        $returnData = [];
        foreach ($previousReturns as $return) {
            foreach ($return->items as $returnItem) {
                $itemId = $returnItem->invoice_item_id;
                $returnedQuantities[$itemId] = ($returnedQuantities[$itemId] ?? 0) + $returnItem->quantity;
                if (!isset($returnData[$itemId])) {
                    $returnData[$itemId] = [];
                }
                $exists = false;
                foreach ($returnData[$itemId] as $existing) {
                    if ($existing['id'] == $return->id) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $returnData[$itemId][] = [
                        'id' => $return->id,
                        'number' => $return->return_number
                    ];
                }
            }
        }
        
        return response()->json([
            'items' => $invoice->items->map(function($item) use ($returnedQuantities, $returnData) {
                $returnedQty = $returnedQuantities[$item->id] ?? 0;
                $availableQty = $item->quantity - $returnedQty;
                
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'returned_quantity' => $returnedQty,
                    'available_quantity' => $availableQty,
                    'unit_price' => $item->unit_price,
                    'previous_returns' => $returnData[$item->id] ?? [],
                ];
            })->filter(fn($item) => $item['available_quantity'] > 0)
        ]);
    }

    public function cancel(CustomerReturn $return)
    {
        try {
            $this->returnService->cancelReturn($return->id, auth()->id());
            return redirect()->route('sales.returns.show', $return->id)
                ->with('success', 'تم إلغاء المرتجع بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pdf(CustomerReturn $return)
    {
        return $this->pdfController->generateReturnPdf($return);
    }
}
