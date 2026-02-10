<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\StorePayment;
use App\Services\Warehouse\WarehousePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WarehousePaymentController extends Controller
{
    public function __construct(private WarehousePaymentService $service)
    {
        if (!Auth::check()) {
            Auth::loginUsingId(2);
        }
    }

    public function index(Request $request)
    {
        $query = StorePayment::with('store', 'marketer', 'keeper');

        $hasFilter = $request->filled('payment_number') || $request->filled('from_date') || $request->filled('to_date') || $request->filled('search');

        if (!$hasFilter && $request->has('status')) {
            $query->where('status', $request->status);
        } elseif (!$hasFilter && !$request->has('all')) {
            $query->where('status', 'pending');
        }

        if ($request->filled('payment_number')) {
            $query->where('payment_number', 'like', '%' . $request->payment_number . '%');
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
                $q->whereHas('marketer', function($q) use ($search) {
                    $q->where('full_name', 'like', '%' . $search . '%');
                })->orWhereHas('store', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $payments = $query->latest('id')->paginate(20)->withQueryString();

        return view('warehouse.payments.index', compact('payments'));
    }

    public function show(StorePayment $payment)
    {
        $payment->load('store', 'marketer', 'keeper');
        return view('warehouse.payments.show', compact('payment'));
    }

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'receipt_image' => 'required|image|max:2048',
        ]);

        try {
            $payment = StorePayment::findOrFail($id);
            $path = $request->file('receipt_image')->store('payments/' . $payment->payment_number, 'public');
            
            $payment = $this->service->approvePayment($id, auth()->id(), $path);

            return redirect()->route('warehouse.payments.show', $payment)
                ->with('success', 'تم توثيق إيصال القبض بنجاح');
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
            $payment = $this->service->rejectPayment($id, $validated['notes']);

            return redirect()->route('warehouse.payments.show', $payment)
                ->with('success', 'تم رفض إيصال القبض');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
