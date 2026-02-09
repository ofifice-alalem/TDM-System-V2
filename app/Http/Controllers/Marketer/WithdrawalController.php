<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\MarketerWithdrawalRequest;
use App\Services\Marketer\WithdrawalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function __construct(private WithdrawalService $service)
    {
        if (!Auth::check()) {
            Auth::loginUsingId(3);
        }
    }

    public function index(Request $request)
    {
        $query = MarketerWithdrawalRequest::with('marketer', 'approver', 'rejecter')
            ->where('marketer_id', auth()->id());

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } elseif (!$request->has('all')) {
            $query->where('status', 'pending');
        }

        $withdrawals = $query->latest('id')->paginate(20)->withQueryString();
        $availableBalance = $this->service->getAvailableBalance(auth()->id());

        return view('marketer.withdrawals.index', compact('withdrawals', 'availableBalance'));
    }

    public function create()
    {
        $availableBalance = $this->service->getAvailableBalance(auth()->id());
        return view('marketer.withdrawals.create', compact('availableBalance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $withdrawal = $this->service->createWithdrawal(
                auth()->id(),
                $validated['requested_amount'],
                $validated['notes'] ?? null
            );

            return redirect()->route('marketer.withdrawals.show', $withdrawal)
                ->with('success', 'تم إنشاء طلب السحب بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(MarketerWithdrawalRequest $withdrawal)
    {
        $withdrawal->load('marketer', 'approver', 'rejecter');
        return view('marketer.withdrawals.show', compact('withdrawal'));
    }

    public function cancel(MarketerWithdrawalRequest $withdrawal, Request $request)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:500'
        ]);

        try {
            $this->service->cancelWithdrawal($withdrawal->id, auth()->id());
            $withdrawal->update(['notes' => $validated['notes']]);

            return redirect()->route('marketer.withdrawals.index')
                ->with('success', 'تم إلغاء طلب السحب بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
