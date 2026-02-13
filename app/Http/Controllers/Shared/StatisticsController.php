<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\SalesInvoice;
use App\Models\StorePayment;
use App\Models\SalesReturn;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::where('is_active', true)->get();
        $results = null;

        if ($request->filled(['stat_type', 'store_id', 'operation', 'from_date', 'to_date'])) {
            $results = $this->getStatistics($request);
        }

        return view('shared.statistics.index', compact('stores', 'results'));
    }

    private function getStatistics($request)
    {
        $query = match($request->operation) {
            'sales' => SalesInvoice::with('marketer', 'store')
                ->where('store_id', $request->store_id),
            'payments' => StorePayment::with('marketer', 'store', 'keeper')
                ->where('store_id', $request->store_id),
            'returns' => SalesReturn::with('marketer', 'store')
                ->where('store_id', $request->store_id),
            default => null
        };

        if (!$query) return null;

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->whereDate('created_at', '>=', $request->from_date)
              ->whereDate('created_at', '<=', $request->to_date);

        $data = $query->latest()->get();

        return [
            'operation' => $request->operation,
            'data' => $data,
            'total' => $data->where('status', 'approved')->sum(fn($item) => match($request->operation) {
                'sales' => $item->total_amount,
                'payments' => $item->amount,
                'returns' => $item->total_amount,
                default => 0
            })
        ];
    }
}
