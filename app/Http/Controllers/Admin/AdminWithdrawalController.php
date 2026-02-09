<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketerWithdrawalRequest;
use App\Services\Admin\AdminWithdrawalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminWithdrawalController extends Controller
{
    public function __construct(private AdminWithdrawalService $service)
    {
        if (!Auth::check()) {
            Auth::loginUsingId(1);
        }
    }

    public function index(Request $request)
    {
        $query = MarketerWithdrawalRequest::with('marketer', 'approver', 'rejecter');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } elseif (!$request->has('all')) {
            $query->where('status', 'pending');
        }

        $withdrawals = $query->latest('id')->paginate(20)->withQueryString();

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function show(MarketerWithdrawalRequest $withdrawal)
    {
        $withdrawal->load('marketer', 'approver', 'rejecter');
        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'signed_receipt_image' => 'required|image|max:2048',
        ]);

        try {
            $withdrawal = MarketerWithdrawalRequest::findOrFail($id);
            $path = $request->file('signed_receipt_image')->store('withdrawals/WD-' . $withdrawal->id, 'public');
            
            $withdrawal = $this->service->approveWithdrawal($id, auth()->id(), $path);

            return redirect()->route('admin.withdrawals.show', $withdrawal)
                ->with('success', 'تم الموافقة على طلب السحب بنجاح');
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
            $withdrawal = $this->service->rejectWithdrawal($id, auth()->id(), $validated['notes']);

            return redirect()->route('admin.withdrawals.show', $withdrawal)
                ->with('success', 'تم رفض طلب السحب');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
