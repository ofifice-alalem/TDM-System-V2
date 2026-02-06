<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\ProductPromotion;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = ProductPromotion::with(['product', 'creator'])
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->paginate(10);

        return view('marketer.promotions.index', compact('promotions'));
    }
}
