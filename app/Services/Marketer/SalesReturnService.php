<?php

namespace App\Services\Marketer;

use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\SalesInvoice;
use App\Models\StoreActualStock;
use App\Models\StoreReturnPendingStock;
use Illuminate\Support\Facades\DB;

class SalesReturnService
{
    public function createReturn($marketerId, array $data)
    {
        return DB::transaction(function () use ($marketerId, $data) {
            $invoice = SalesInvoice::findOrFail($data['sales_invoice_id']);

            if ($invoice->marketer_id != $marketerId) {
                throw new \Exception('هذه الفاتورة لا تخصك');
            }

            if ($invoice->status !== 'approved') {
                throw new \Exception('لا يمكن إرجاع بضاعة من فاتورة غير موثقة');
            }

            // Validate quantities
            foreach ($data['items'] as $item) {
                $invoiceItem = $invoice->items()->find($item['sales_invoice_item_id']);
                if (!$invoiceItem) {
                    throw new \Exception('المنتج غير موجود في الفاتورة');
                }

                $totalQuantity = $invoiceItem->quantity + $invoiceItem->free_quantity;
                $alreadyReturned = SalesReturnItem::whereHas('salesReturn', function ($q) use ($invoice) {
                    $q->where('sales_invoice_id', $invoice->id)
                      ->whereIn('status', ['pending', 'approved']);
                })->where('sales_invoice_item_id', $invoiceItem->id)
                  ->sum('quantity');

                if ($item['quantity'] > ($totalQuantity - $alreadyReturned)) {
                    throw new \Exception("الكمية المرجعة للمنتج {$invoiceItem->product->name} أكبر من المتاح");
                }

                // Check store stock
                $storeStock = StoreActualStock::where('store_id', $invoice->store_id)
                    ->where('product_id', $invoiceItem->product_id)
                    ->first();

                if (!$storeStock || $storeStock->quantity < $item['quantity']) {
                    throw new \Exception("المتجر لا يملك الكمية الكافية من {$invoiceItem->product->name}");
                }
            }

            // Create return
            $returnNumber = 'RET-' . date('Ymd') . '-' . str_pad(SalesReturn::count() + 1, 4, '0', STR_PAD_LEFT);
            
            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $invoiceItem = $invoice->items()->find($item['sales_invoice_item_id']);
                $totalAmount += $item['quantity'] * $invoiceItem->unit_price;
            }

            $return = SalesReturn::create([
                'return_number' => $returnNumber,
                'sales_invoice_id' => $invoice->id,
                'store_id' => $invoice->store_id,
                'marketer_id' => $marketerId,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // Create items & update stocks
            foreach ($data['items'] as $item) {
                $invoiceItem = $invoice->items()->find($item['sales_invoice_item_id']);

                SalesReturnItem::create([
                    'return_id' => $return->id,
                    'sales_invoice_item_id' => $invoiceItem->id,
                    'product_id' => $invoiceItem->product_id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $invoiceItem->unit_price,
                ]);

                // Deduct from store actual stock
                $storeStock = StoreActualStock::where('store_id', $invoice->store_id)
                    ->where('product_id', $invoiceItem->product_id)
                    ->first();
                $storeStock->decrement('quantity', $item['quantity']);

                // Add to pending stock
                StoreReturnPendingStock::create([
                    'return_id' => $return->id,
                    'store_id' => $invoice->store_id,
                    'product_id' => $invoiceItem->product_id,
                    'quantity' => $item['quantity'],
                ]);
            }

            return $return;
        });
    }

    public function cancelReturn(SalesReturn $return, $notes)
    {
        if (!in_array($return->status, ['pending', 'approved'])) {
            throw new \Exception('لا يمكن إلغاء هذا الطلب');
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
                'status' => 'cancelled',
                'notes' => $notes,
            ]);

            return $return;
        });
    }
}
