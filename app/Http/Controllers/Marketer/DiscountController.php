<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\InvoiceDiscountTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            Auth::loginUsingId(3); // Marketer
        }
    }

    public function index(Request $request)
    {
        $query = InvoiceDiscountTier::with('creator')
            ->where('is_active', true)
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('min_amount');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('discount_type', $request->type);
        } else {
            $query->where('discount_type', 'percentage');
        }

        $discounts = $query->paginate(20)->withQueryString();

        return view('marketer.discounts.index', compact('discounts'));
    }
}
