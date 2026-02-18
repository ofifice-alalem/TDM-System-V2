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

    public function index()
    {
        $payments = CustomerPayment::with(['customer', 'salesUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('sales.payments.index', compact('payments'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)
            ->whereHas('debtLedger')
            ->get()
            ->filter(fn($c) => $c->total_debt > 0);

        return view('sales.payments.create', compact('customers'));
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
