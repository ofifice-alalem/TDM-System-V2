<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\Sales\CustomerPaymentController as SharedCustomerPaymentController;
use App\Models\CustomerPayment;
use App\Models\Customer;
use App\Services\Sales\CustomerPaymentService;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{
    public function __construct(
        private CustomerPaymentService $paymentService,
        private SharedCustomerPaymentController $pdfController
    )
    {
    }

    public function index(Request $request)
    {
        $query = CustomerPayment::with(['customer', 'salesUser']);

        if ($request->filled('payment_number')) {
            $query->where('payment_number', 'like', '%' . $request->payment_number . '%');
        }

        if ($request->filled('customer')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('amount_from')) {
            $query->where('amount', '>=', $request->amount_from);
        }

        if ($request->filled('amount_to')) {
            $query->where('amount', '<=', $request->amount_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('sales.payments.index', compact('payments'));
    }

    public function create()
    {
        return view('sales.payments.create');
    }

    public function searchCustomers(Request $request)
    {
        $query = $request->get('query');
        
        $customers = Customer::where('is_active', true)
            ->whereHas('debtLedger')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('phone', 'like', '%' . $query . '%');
            })
            ->get(['id', 'name', 'phone'])
            ->filter(fn($c) => $c->total_debt > 0)
            ->values();

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,transfer,check',
            'notes' => 'nullable|string',
        ]);

        try {
            $payment = $this->paymentService->createPayment(
                auth()->id(),
                $validated['customer_id'],
                $validated['amount'],
                $validated['payment_method'],
                $validated['notes'] ?? null
            );

            return redirect()->route('sales.payments.show', $payment->id)
                ->with('success', 'تم تسجيل الدفعة بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(CustomerPayment $payment)
    {
        $payment->load(['customer', 'salesUser']);
        return view('sales.payments.show', compact('payment'));
    }

    public function getCustomerDebt($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        return response()->json(['debt' => $customer->total_debt]);
    }

    public function pdf(CustomerPayment $payment)
    {
        return $this->pdfController->generatePaymentPdf($payment);
    }

    public function cancel(CustomerPayment $payment, Request $request)
    {
        try {
            $this->paymentService->cancelPayment($payment, $request->cancel_notes);
            return redirect()->route('sales.payments.show', $payment->id)
                ->with('success', 'تم إلغاء الدفعة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
