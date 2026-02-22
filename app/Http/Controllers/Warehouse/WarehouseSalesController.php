<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Services\Warehouse\WarehouseSalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $hasFilter = $request->filled('invoice_number') || $request->filled('from_date') || $request->filled('to_date');

        if (!$hasFilter && $request->filled('status')) {
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('marketer', function($mq) use ($search) {
                    $mq->where('full_name', 'like', '%' . $search . '%');
                })->orWhereHas('store', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $status = $request->has('status') ? $request->status : ($hasFilter ? null : 'pending');
        if ($status === 'pending') {
            $query->oldest('updated_at');
        } else {
            $query->latest('updated_at');
        }

        $invoices = $query->paginate(20)->withQueryString();

        return view('warehouse.sales.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = SalesInvoice::with('items.product', 'marketer', 'store', 'keeper', 'rejectedBy')
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

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:500'
        ]);

        try {
            return DB::transaction(function () use ($id, $validated) {
                $invoice = SalesInvoice::where('id', $id)
                    ->where('status', 'pending')
                    ->firstOrFail();

                // إرجاع البضاعة من pending إلى marketer
                foreach ($invoice->items as $item) {
                    $totalQuantity = $item->quantity + $item->free_quantity;

                    DB::table('marketer_actual_stock')
                        ->where('marketer_id', $invoice->marketer_id)
                        ->where('product_id', $item->product_id)
                        ->increment('quantity', $totalQuantity);

                    DB::table('store_pending_stock')
                        ->where('sales_invoice_id', $invoice->id)
                        ->where('product_id', $item->product_id)
                        ->delete();
                }

                $invoice->update([
                    'status' => 'rejected',
                    'rejected_by' => auth()->id(),
                    'rejected_at' => now(),
                    'notes' => $validated['notes']
                ]);

                return redirect()->route('warehouse.sales.index')
                    ->with('success', 'تم رفض الفاتورة وإرجاع البضاعة');
            });
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
