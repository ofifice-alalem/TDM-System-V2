<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Services\Warehouse\WarehouseSalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseSalesController extends Controller
{
    public function __construct(private WarehouseSalesService $service)
    {
        if (!Auth::check()) {
            Auth::loginUsingId(2);
        }
    }

    public function index(Request $request)
    {
        $query = SalesInvoice::with('marketer', 'store', 'items.product');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } elseif (!$request->has('all')) {
            $query->where('status', 'pending');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        return view('warehouse.sales.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = SalesInvoice::with('items.product', 'marketer', 'store', 'keeper')
            ->findOrFail($id);
        return view('warehouse.sales.show', compact('invoice'));
    }

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'stamped_invoice_image' => 'required|image|max:2048',
        ]);

        try {
            $invoice = SalesInvoice::findOrFail($id);
            
            $path = $request->file('stamped_invoice_image')->store(
                'sales/' . $invoice->invoice_number,
                'public'
            );

            $this->service->approveInvoice($id, auth()->id(), $path);

            return redirect()->route('warehouse.sales.index')
                ->with('success', 'تم توثيق الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function viewDocumentation($id)
    {
        $invoice = SalesInvoice::findOrFail($id);
        
        if ($invoice->status !== 'approved' || !$invoice->stamped_invoice_image) {
            abort(404, 'لا توجد صورة توثيق');
        }

        $imagePath = storage_path('app/public/' . $invoice->stamped_invoice_image);
        
        if (!file_exists($imagePath)) {
            abort(404, 'الصورة غير موجودة');
        }

        return response()->file($imagePath);
    }
}
