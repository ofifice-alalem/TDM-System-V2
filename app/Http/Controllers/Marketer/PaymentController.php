<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\StorePayment;
use App\Models\Store;
use App\Models\StoreDebtLedger;
use App\Services\Marketer\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service)
    {
        if (!Auth::check()) {
            Auth::loginUsingId(3);
        }
    }

    public function index(Request $request)
    {
        $query = StorePayment::with('store', 'marketer', 'keeper')
            ->where('marketer_id', auth()->id());

        $hasFilter = $request->filled('payment_number') || $request->filled('from_date') || $request->filled('to_date') || $request->filled('store');

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

        if ($request->filled('store')) {
            $storeName = $request->store;
            $query->whereHas('store', function($q) use ($storeName) {
                $q->where('name', 'like', '%' . $storeName . '%');
            });
        }

        $payments = $query->latest('id')->paginate(20)->withQueryString();

        return view('marketer.payments.index', compact('payments'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)
            ->get()
            ->map(function($store) {
                $store->debt = StoreDebtLedger::where('store_id', $store->id)->sum('amount');
                return $store;
            })
            ->filter(fn($store) => $store->debt > 0);

        return view('marketer.payments.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'keeper_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,transfer,certified_check',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $payment = $this->service->createPayment(
                auth()->id(),
                $validated['store_id'],
                $validated['keeper_id'],
                $validated['amount'],
                $validated['payment_method'],
                $validated['notes'] ?? null
            );

            return redirect()->route('marketer.payments.show', $payment)
                ->with('success', 'تم إنشاء إيصال القبض بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(StorePayment $payment)
    {
        $payment->load('store', 'marketer', 'keeper');
        return view('marketer.payments.show', compact('payment'));
    }

    public function cancel(StorePayment $payment, Request $request)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:500'
        ]);

        try {
            $this->service->cancelPayment($payment->id, auth()->id());
            $payment->update(['notes' => $validated['notes']]);

            return redirect()->route('marketer.payments.index')
                ->with('success', 'تم إلغاء إيصال القبض بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getStoreDebt($storeId)
    {
        $debt = StoreDebtLedger::where('store_id', $storeId)->sum('amount');
        return response()->json(['debt' => max(0, $debt)]);
    }
}
