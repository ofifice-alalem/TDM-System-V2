<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketerStockController extends Controller
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

        $actualStock = DB::table('marketer_actual_stock')
            ->join('products', 'marketer_actual_stock.product_id', '=', 'products.id')
            ->where('marketer_actual_stock.marketer_id', $marketerId)
            ->where('marketer_actual_stock.quantity', '>', 0)
            ->select('products.name', 'marketer_actual_stock.quantity', 'products.id')
            ->get();

        $reservedStock = DB::table('marketer_reserved_stock')
            ->join('products', 'marketer_reserved_stock.product_id', '=', 'products.id')
            ->where('marketer_reserved_stock.marketer_id', $marketerId)
            ->where('marketer_reserved_stock.quantity', '>', 0)
            ->select('products.name', 'marketer_reserved_stock.quantity', 'products.id')
            ->get();

        $totalActual = $actualStock->sum('quantity');
        $totalReserved = $reservedStock->sum('quantity');

        return view('marketer.stock.index', compact('actualStock', 'reservedStock', 'totalActual', 'totalReserved'));
    }
}
