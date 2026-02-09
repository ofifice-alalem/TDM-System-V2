<?php

namespace App\Services\Warehouse;

use App\Models\SalesInvoice;
use App\Models\StoreDebtLedger;
use Illuminate\Support\Facades\DB;

class WarehouseSalesService
{
    public function approveInvoice($invoiceId, $keeperId, $stampedImage)
    {
        return DB::transaction(function () use ($invoiceId, $keeperId, $stampedImage) {
            $invoice = SalesInvoice::where('id', $invoiceId)
                ->where('status', 'pending')
                ->firstOrFail();

            foreach ($invoice->items as $item) {
                $totalQuantity = $item->quantity + $item->free_quantity;

                DB::table('store_pending_stock')
                    ->where('sales_invoice_id', $invoice->id)
                    ->where('product_id', $item->product_id)
                    ->delete();

                DB::table('store_actual_stock')->updateOrInsert(
                    ['store_id' => $invoice->store_id, 'product_id' => $item->product_id],
                    ['quantity' => DB::raw("quantity + {$totalQuantity}")]
                );
            }

            StoreDebtLedger::create([
                'store_id' => $invoice->store_id,
                'entry_type' => 'sale',
                'sales_invoice_id' => $invoice->id,
                'amount' => $invoice->total_amount,
                'created_at' => now(),
            ]);

            $invoice->update([
                'status' => 'approved',
                'keeper_id' => $keeperId,
                'stamped_invoice_image' => $stampedImage,
                'confirmed_at' => now(),
            ]);

            return $invoice;
        });
    }
}
