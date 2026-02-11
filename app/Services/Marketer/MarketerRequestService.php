<?php

namespace App\Services\Marketer;

use App\Models\MarketerRequest;
use App\Models\MarketerRequestItem;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class MarketerRequestService
{
    public function __construct(private NotificationService $notificationService)
    {
    }
    public function createRequest($marketerId, array $items, $notes = null)
    {
        return DB::transaction(function () use ($marketerId, $items, $notes) {
            $request = MarketerRequest::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'marketer_id' => $marketerId,
                'status' => 'pending',
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                MarketerRequestItem::create([
                    'request_id' => $request->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // إرسال إشعار لجميع أمناء المخزن
            $warehouseKeepers = \App\Models\User::where('role_id', 2)->where('is_active', true)->get();
            foreach ($warehouseKeepers as $keeper) {
                $this->notificationService->create(
                    $keeper->id,
                    'marketer_request_created',
                    'طلب بضاعة جديد',
                    'تم إنشاء طلب بضاعة جديد رقم ' . $request->invoice_number,
                    route('warehouse.requests.show', $request->id)
                );
            }

            return $request->load('items.product');
        });
    }

    public function cancelRequest($requestId, $marketerId, $notes = null)
    {
        return DB::transaction(function () use ($requestId, $marketerId, $notes) {
            $request = MarketerRequest::where('id', $requestId)
                ->where('marketer_id', $marketerId)
                ->whereIn('status', ['pending', 'approved'])
                ->firstOrFail();

            if ($request->status === 'approved') {
                $this->returnStockFromReserved($request);
            }

            $request->update([
                'status' => 'cancelled',
                'notes' => $notes
            ]);
            return $request;
        });
    }

    private function returnStockFromReserved($request)
    {
        foreach ($request->items as $item) {
            DB::table('main_stock')
                ->where('product_id', $item->product_id)
                ->increment('quantity', $item->quantity);

            DB::table('marketer_reserved_stock')
                ->where('marketer_id', $request->marketer_id)
                ->where('product_id', $item->product_id)
                ->decrement('quantity', $item->quantity);
        }
    }

    private function generateInvoiceNumber()
    {
        return 'MR-' . date('Ymd') . '-' . str_pad(MarketerRequest::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
