<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MarketerCommission;
use App\Models\MarketerWithdrawalRequest;
use Illuminate\Http\Request;

class MarketerDetailsController extends Controller
{
    public function show($id)
    {
        $marketer = User::where('role_id', 3)->findOrFail($id);
        
        $totalCommissions = MarketerCommission::where('marketer_id', $id)->sum('commission_amount');
        
        $totalWithdrawals = MarketerWithdrawalRequest::where('marketer_id', $id)
            ->where('status', 'approved')
            ->sum('requested_amount');
        
        $availableBalance = $totalCommissions - $totalWithdrawals;
        
        $commissions = MarketerCommission::with(['store', 'payment'])
            ->where('marketer_id', $id)
            ->latest()
            ->limit(20)
            ->get()
            ->map(function($commission) {
                return [
                    'type' => 'commission',
                    'amount' => $commission->commission_amount,
                    'rate' => $commission->commission_rate,
                    'payment_amount' => $commission->payment_amount,
                    'store_name' => $commission->store->name ?? '-',
                    'payment_id' => $commission->payment_id,
                    'date' => $commission->created_at,
                ];
            });
        
        $withdrawals = MarketerWithdrawalRequest::where('marketer_id', $id)
            ->where('status', 'approved')
            ->latest()
            ->limit(20)
            ->get()
            ->map(function($withdrawal) {
                return [
                    'type' => 'withdrawal',
                    'amount' => $withdrawal->requested_amount,
                    'id' => $withdrawal->id,
                    'date' => $withdrawal->approved_at ?? $withdrawal->created_at,
                ];
            });
        
        $recentTransactions = $commissions->concat($withdrawals)
            ->sortByDesc('date')
            ->take(20);
        
        return view('admin.users.marketer-details', compact(
            'marketer',
            'totalCommissions',
            'totalWithdrawals',
            'availableBalance',
            'recentTransactions'
        ));
    }
}
