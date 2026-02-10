<?php

namespace App\Services\Warehouse;

use App\Models\SalesReturn;
use App\Models\MarketerActualStock;
use App\Models\StoreReturnPendingStock;
use App\Models\StoreDebtLedger;
use App\Models\StoreActualStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WarehouseSalesReturnService
{
    public function approveReturn(SalesReturn $return, $keeperId, $stampedImagePath = null)
    {
        if ($return->status !== 'pending') {
            throw new \Exception('لا يمكن الموافقة على هذا الطلب');
        }

        return DB::transaction(function () use ($return, $keeperId, $stampedImagePath) {
            // Move from pending to marketer actual stock
            foreach ($return->items as $item) {
                $marketerStock = MarketerActualStock::firstOrCreate(
                    [
                        'marketer_id' => $return->marketer_id,
                        'product_id' => $item->product_id,
                    ],
                    ['quantity' => 0]
                );
                $marketerStock->increment('quantity', $item->quantity);
            }

            // Delete pending stock
            StoreReturnPendingStock::where('return_id', $return->id)->delete();

            // Record debt reduction
            StoreDebtLedger::create([
                'store_id' => $return->store_id,
                'entry_type' => 'return',
                'return_id' => $return->id,
                'amount' => -$return->total_amount,
            ]);

            $return->update([
                'status' => 'approved',
                'keeper_id' => $keeperId,
                'stamped_image' => $stampedImagePath,
                'confirmed_at' => now(),
            ]);

            return $return;
        });
    }

    public function rejectReturn(SalesReturn $return, $notes)
    {
        if ($return->status !== 'pending') {
            throw new \Exception('لا يمكن رفض هذا الطلب');
        }

        return DB::transaction(function () use ($return, $notes) {
            // Return stock to store
            foreach ($return->items as $item) {
                $storeStock = StoreActualStock::where('store_id', $return->store_id)
                    ->where('product_id', $item->product_id)
                    ->first();
                $storeStock->increment('quantity', $item->quantity);
            }

            // Delete pending stock
            StoreReturnPendingStock::where('return_id', $return->id)->delete();

            $return->update([
                'status' => 'rejected',
                'notes' => $notes,
            ]);

            return $return;
        });
    }
}
