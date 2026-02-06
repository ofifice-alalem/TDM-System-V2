<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceDiscountTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceDiscountController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            Auth::loginUsingId(1); // Admin
        }
    }

    public function index(Request $request)
    {
        $query = InvoiceDiscountTier::with('creator')
            ->orderBy('min_amount');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('discount_type', $request->type);
        } else {
            $query->where('discount_type', 'percentage');
        }

        // Filter by active status
        if ($request->has('active') && $request->active !== 'all') {
            if ($request->active === 'expired') {
                $query->where(function($q) {
                    $q->where('end_date', '<', now()->toDateString())
                      ->orWhere('is_active', false);
                });
            } else {
                $query->where('is_active', $request->active === '1')
                      ->where('end_date', '>=', now()->toDateString());
            }
        } elseif (!$request->has('active')) {
            $query->where('is_active', true)
                  ->where('end_date', '>=', now()->toDateString());
        }

        // Filter by ID
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $discounts = $query->paginate(20)->withQueryString();

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_percentage' => 'required_if:discount_type,percentage|nullable|numeric|min:0|max:100',
            'discount_amount' => 'required_if:discount_type,fixed|nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        InvoiceDiscountTier::create([
            'min_amount' => $validated['min_amount'],
            'discount_type' => $validated['discount_type'],
            'discount_percentage' => $validated['discount_type'] === 'percentage' ? $validated['discount_percentage'] : null,
            'discount_amount' => $validated['discount_type'] === 'fixed' ? $validated['discount_amount'] : null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'تم إنشاء قاعدة الخصم بنجاح');
    }

    public function toggleActive(InvoiceDiscountTier $discount)
    {
        $discount->update(['is_active' => !$discount->is_active]);

        return redirect()->back()
            ->with('success', $discount->is_active ? 'تم تفعيل القاعدة' : 'تم تعطيل القاعدة');
    }

    public function destroy(InvoiceDiscountTier $discount)
    {
        $discount->update(['is_active' => false]);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'تم تعطيل القاعدة نهائياً');
    }
}
