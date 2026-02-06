<?php

namespace App\Services\Warehouse;

use App\Models\MarketerReturnRequest;
use App\Models\WarehouseStockLog;
use Illuminate\Support\Facades\DB;

class WarehouseReturnService
{
    public function approveReturn($returnId, $keeperId)
    {
        return DB::transaction(function () use ($returnId, $keeperId) {
            $return = MarketerReturnRequest::where('id', $returnId)
                ->where('status', 'pending')
                ->firstOrFail();

            $this->checkStockAvailability($return);

            $return->update([
                'status' => 'approved',
                'approved_by' => $keeperId,
                'approved_at' => now(),
            ]);

            return $return;
        });
    }

    public function rejectReturn($returnId, $keeperId, $notes)
    {
        return DB::transaction(function () use ($returnId, $keeperId, $notes) {
            $return = MarketerReturnRequest::where('id', $returnId)
                ->whereIn('status', ['pending', 'approved'])
                ->firstOrFail();

            $return->update([
                'status' => 'rejected',
                'rejected_by' => $keeperId,
                'rejected_at' => now(),
                'notes' => $notes,
            ]);

            return $return;
        });
    }

    public function documentReturn($returnId, $keeperId, $stampedImage)
    {
        return DB::transaction(function () use ($returnId, $keeperId, $stampedImage) {
            $return = MarketerReturnRequest::where('id', $returnId)
                ->where('status', 'approved')
                ->firstOrFail();

            $this->moveStockToMain($return);

            $return->update([
                'status' => 'documented',
                'documented_by' => $keeperId,
                'documented_at' => now(),
                'stamped_image' => $stampedImage,
            ]);

            WarehouseStockLog::create([
                'invoice_type' => 'marketer_return',
                'invoice_id' => $return->id,
                'keeper_id' => $keeperId,
                'action' => 'return',
            ]);

            return $return;
        });
    }

    private function checkStockAvailability($return)
    {
        foreach ($return->items as $item) {
            $available = DB::table('marketer_actual_stock')
                ->where('marketer_id', $return->marketer_id)
                ->where('product_id', $item->product_id)
                ->value('quantity') ?? 0;

            if ($available < $item->quantity) {
                throw new \Exception("المنتج {$item->product->name} غير متوفر بالكمية المطلوبة في مخزون المسوق");
            }
        }
    }

    private function moveStockToMain($return)
    {
        foreach ($return->items as $item) {
            DB::table('marketer_actual_stock')
                ->where('marketer_id', $return->marketer_id)
                ->where('product_id', $item->product_id)
                ->decrement('quantity', $item->quantity);

            DB::table('main_stock')
                ->where('product_id', $item->product_id)
                ->increment('quantity', $item->quantity);
        }
    }
}
