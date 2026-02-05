<?php

namespace App\Services\Warehouse;

use App\Models\MarketerRequest;
use App\Models\WarehouseStockLog;
use Illuminate\Support\Facades\DB;

class WarehouseRequestService
{
    public function approveRequest($requestId, $keeperId)
    {
        return DB::transaction(function () use ($requestId, $keeperId) {
            $request = MarketerRequest::where('id', $requestId)
                ->where('status', 'pending')
                ->firstOrFail();

            $this->checkStockAvailability($request);
            $this->reserveStock($request);

            $request->update([
                'status' => 'approved',
                'approved_by' => $keeperId,
                'approved_at' => now(),
            ]);

            return $request;
        });
    }

    public function rejectRequest($requestId, $keeperId, $notes)
    {
        return DB::transaction(function () use ($requestId, $keeperId, $notes) {
            $request = MarketerRequest::where('id', $requestId)
                ->whereIn('status', ['pending', 'approved'])
                ->firstOrFail();

            if ($request->status === 'approved') {
                $this->returnStockFromReserved($request);
            }

            $request->update([
                'status' => 'rejected',
                'rejected_by' => $keeperId,
                'rejected_at' => now(),
                'notes' => $notes,
            ]);

            return $request;
        });
    }

    public function documentRequest($requestId, $keeperId, $stampedImage)
    {
        return DB::transaction(function () use ($requestId, $keeperId, $stampedImage) {
            $request = MarketerRequest::where('id', $requestId)
                ->where('status', 'approved')
                ->firstOrFail();

            $this->moveStockToActual($request);

            $request->update([
                'status' => 'documented',
                'documented_by' => $keeperId,
                'documented_at' => now(),
                'stamped_image' => $stampedImage,
            ]);

            WarehouseStockLog::create([
                'invoice_type' => 'marketer_request',
                'invoice_id' => $request->id,
                'keeper_id' => $keeperId,
                'action' => 'withdraw',
            ]);

            return $request;
        });
    }

    private function checkStockAvailability($request)
    {
        foreach ($request->items as $item) {
            $available = DB::table('main_stock')
                ->where('product_id', $item->product_id)
                ->value('quantity') ?? 0;

            if ($available < $item->quantity) {
                throw new \Exception("المنتج {$item->product->name} غير متوفر بالكمية المطلوبة");
            }
        }
    }

    private function reserveStock($request)
    {
        foreach ($request->items as $item) {
            DB::table('main_stock')
                ->where('product_id', $item->product_id)
                ->decrement('quantity', $item->quantity);

            DB::table('marketer_reserved_stock')->updateOrInsert(
                ['marketer_id' => $request->marketer_id, 'product_id' => $item->product_id],
                ['quantity' => DB::raw("quantity + {$item->quantity}")]
            );
        }
    }

    private function moveStockToActual($request)
    {
        foreach ($request->items as $item) {
            DB::table('marketer_reserved_stock')
                ->where('marketer_id', $request->marketer_id)
                ->where('product_id', $item->product_id)
                ->decrement('quantity', $item->quantity);

            DB::table('marketer_actual_stock')->updateOrInsert(
                ['marketer_id' => $request->marketer_id, 'product_id' => $item->product_id],
                ['quantity' => DB::raw("quantity + {$item->quantity}")]
            );
        }
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
}
