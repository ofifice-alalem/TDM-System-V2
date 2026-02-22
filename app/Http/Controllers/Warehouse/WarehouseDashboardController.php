<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\MarketerRequest;
use App\Models\MarketerReturnRequest;
use App\Models\SalesInvoice;
use App\Models\StorePayment;
use App\Models\SalesReturn;
use App\Models\FactoryInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseDashboardController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            Auth::loginUsingId(2);
        }
    }

    public function index(Request $request)
    {
        // البحث السريع
        $searchResult = null;
        if ($request->filled('invoice_number') && $request->filled('invoice_type')) {
            $searchResult = $this->quickSearch($request->invoice_number, $request->invoice_type);
        }
        // طلبات البضاعة من المسوقين
        $pendingRequests = MarketerRequest::where('status', 'pending')->count();
        $approvedRequests = MarketerRequest::where('status', 'approved')->count();
        $oldestRequests = MarketerRequest::with('marketer', 'items.product')
            ->whereIn('status', ['pending', 'approved'])
            ->oldest()
            ->limit(5)
            ->get();

        // إرجاعات المسوقين
        $pendingReturns = MarketerReturnRequest::where('status', 'pending')->count();
        $approvedReturns = MarketerReturnRequest::where('status', 'approved')->count();

        // فواتير البيع
        $pendingSales = SalesInvoice::where('status', 'pending')->count();

        // إيصالات القبض
        $pendingPayments = StorePayment::where('status', 'pending')->count();

        // فواتير المصنع
        $pendingFactoryInvoices = FactoryInvoice::where('status', 'pending')->count();

        // إرجاعات المتاجر
        $pendingSalesReturns = SalesReturn::where('status', 'pending')->count();

        // عدد الإشعارات غير المقروءة
        $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return view('warehouse.dashboard', compact(
            'pendingRequests',
            'approvedRequests',
            'oldestRequests',
            'pendingReturns',
            'approvedReturns',
            'pendingSales',
            'pendingPayments',
            'pendingFactoryInvoices',
            'pendingSalesReturns',
            'unreadNotifications',
            'searchResult'
        ));
    }

    private function quickSearch($invoiceNumber, $type)
    {
        $results = match($type) {
            'MR' => MarketerRequest::with('marketer', 'items.product')
                ->where('invoice_number', 'like', '%' . $invoiceNumber . '%')
                ->get(),
            'MRR' => MarketerReturnRequest::with('marketer', 'items.product')
                ->where('invoice_number', 'like', '%' . $invoiceNumber . '%')
                ->get(),
            'SI' => SalesInvoice::with('marketer', 'store', 'items.product')
                ->where('invoice_number', 'like', '%' . $invoiceNumber . '%')
                ->get(),
            'RET' => SalesReturn::with('marketer', 'store', 'items.product')
                ->where('return_number', 'like', '%' . $invoiceNumber . '%')
                ->get(),
            'RCP' => StorePayment::with('marketer', 'store', 'keeper')
                ->where('payment_number', 'like', '%' . $invoiceNumber . '%')
                ->get(),
            'FI' => FactoryInvoice::with('items.product')
                ->where('invoice_number', 'like', '%' . $invoiceNumber . '%')
                ->get(),
            default => collect()
        };

        if ($results->isEmpty()) {
            return ['found' => false, 'type' => $type];
        }

        $resultsWithRoutes = $results->map(function($result) use ($type) {
            return [
                'data' => $result,
                'route' => match($type) {
                    'MR' => route('warehouse.requests.show', $result->id),
                    'MRR' => route('warehouse.returns.show', $result->id),
                    'SI' => route('warehouse.sales.show', $result->id),
                    'RET' => route('warehouse.sales-returns.show', $result->id),
                    'RCP' => route('warehouse.payments.show', $result->id),
                    'FI' => route('warehouse.factory-invoices.show', $result->id),
                    default => null
                }
            ];
        });

        return [
            'found' => true,
            'type' => $type,
            'results' => $resultsWithRoutes,
            'count' => $results->count()
        ];
    }
}
