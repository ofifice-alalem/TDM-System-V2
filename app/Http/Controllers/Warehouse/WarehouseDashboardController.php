<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\MarketerRequest;
use App\Models\MarketerReturnRequest;
use App\Models\SalesInvoice;
use App\Models\StorePayment;
use App\Models\SalesReturn;
use Illuminate\Support\Facades\Auth;

class WarehouseDashboardController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            Auth::loginUsingId(2);
        }
    }

    public function index()
    {
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
            'pendingSalesReturns',
            'unreadNotifications'
        ));
    }
}
