<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\MarketerCommission;
use App\Models\MarketerWithdrawal;
use Illuminate\Support\Facades\Auth;

class CommissionController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            Auth::loginUsingId(3);
        }
    }

    public function index()
    {
        $marketerId = auth()->id();

        $totalCommissions = MarketerCommission::where('marketer_id', $marketerId)->sum('commission_amount');
        $totalWithdrawals = MarketerWithdrawal::where('marketer_id', $marketerId)
            ->where('status', 'approved')
            ->sum('amount');
        $availableBalance = $totalCommissions - $totalWithdrawals;

        $commissions = MarketerCommission::where('marketer_id', $marketerId)
            ->with(['store', 'payment'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function($commission) {
                return [
                    'type' => 'commission',
                    'id' => $commission->id,
                    'payment_id' => $commission->payment_id,
                    'store_name' => $commission->store->name ?? 'غير متوفر',
                    'amount' => $commission->commission_amount,
                    'rate' => $commission->commission_rate,
                    'payment_amount' => $commission->payment_amount,
                    'date' => $commission->created_at,
                ];
            });

        $withdrawals = MarketerWithdrawal::where('marketer_id', $marketerId)
            ->latest()
            ->take(10)
            ->get()
            ->map(function($withdrawal) {
                return [
                    'type' => 'withdrawal',
                    'id' => $withdrawal->id,
                    'amount' => $withdrawal->amount,
                    'status' => $withdrawal->status,
                    'date' => $withdrawal->created_at,
                ];
            });

        $recentTransactions = $commissions->concat($withdrawals)
            ->sortByDesc('date')
            ->take(10)
            ->values();

        return view('marketer.commissions.index', compact(
            'totalCommissions',
            'totalWithdrawals',
            'availableBalance',
            'recentTransactions'
        ));
    }
}
