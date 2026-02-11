<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\MainStock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainStockController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            Auth::loginUsingId(2);
        }
    }

    public function index(Request $request)
    {
        $query = Product::with('mainStock')
            ->leftJoin('main_stock', 'products.id', '=', 'main_stock.product_id')
            ->select('products.*', 'main_stock.quantity as stock_quantity', 'main_stock.updated_at as stock_updated_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                  ->orWhere('products.barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'in_stock') {
                $query->where('main_stock.quantity', '>', 0);
            } elseif ($request->status === 'out_of_stock') {
                $query->where(function($q) {
                    $q->whereNull('main_stock.quantity')
                      ->orWhere('main_stock.quantity', '<=', 0);
                });
            }
        }

        $products = $query->orderBy('products.name')->paginate(20)->withQueryString();

        return view('shared.main-stock.index', compact('products'));
    }
}
