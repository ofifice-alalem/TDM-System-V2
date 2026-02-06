<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductPromotion;
use Illuminate\Http\Request;

class ProductPromotionController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductPromotion::with(['product', 'creator']);

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('product')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->product . '%');
            });
        }

        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Status filter
        $activeFilter = $request->get('active', '1');
        
        if ($activeFilter === 'expired') {
            $query->where('is_active', true)
                  ->where('end_date', '<', now()->startOfDay());
        } elseif ($activeFilter === '0') {
            $query->where('is_active', false);
        } elseif ($activeFilter === '1') {
            $query->where('is_active', true)
                  ->where('end_date', '>=', now()->startOfDay());
        }
        // 'all' = no filter

        $promotions = $query->latest()->paginate(10)->withQueryString();

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'min_quantity' => 'required|integer|min:1',
            'free_quantity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $validated['created_by'] = auth()->id() ?? 1;

        ProductPromotion::create($validated);

        return redirect()->route('admin.promotions.index')
            ->with('success', 'تم إنشاء العرض الترويجي بنجاح');
    }

    public function toggleActive(ProductPromotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);

        return redirect()->back()
            ->with('success', 'تم تحديث حالة العرض بنجاح');
    }

    public function destroy(ProductPromotion $promotion)
    {
        $promotion->update(['is_active' => false]);

        return redirect()->back()
            ->with('success', 'تم تعطيل العرض بنجاح');
    }
}
