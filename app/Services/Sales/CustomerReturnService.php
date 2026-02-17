<?php

namespace App\Services\Sales;

use App\Models\CustomerReturn;
use App\Models\CustomerReturnItem;
use App\Models\CustomerDebtLedger;
use Illuminate\Support\Facades\DB;

class CustomerReturnService
{
    public function createReturn($salesUserId, $invoiceId, $customerId, array $items, $notes = null)
    {
        return DB::transaction(function () use ($salesUserId, $invoiceId, $customerId, $items, $notes) {
            $totalAmount = 0;
            $returnItems = [];

            foreach ($items as $item) {
                $invoiceItem = DB::table('customer_invoice_items')->find($item['invoice_item_id']);
                
                if ($item['quantity'] > $invoiceItem->quantity) {
                    throw new \Exception("الكمية المرجعة أكبر من الكمية في الفاتورة");
                }

                $totalPrice = $item['quantity'] * $invoiceItem->unit_price;
                $totalAmount += $totalPrice;

                $returnItems[] = [
                    'invoice_item_id' => $item['invoice_item_id'],
                    'product_id' => $invoiceItem->product_id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $invoiceItem->unit_price,
                    'total_price' => $totalPrice,
                ];
            }

            $return = CustomerReturn::create([
                'return_number' => $this->generateReturnNumber(),
                'invoice_id' => $invoiceId,
                'customer_id' => $customerId,
                'sales_user_id' => $salesUserId,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'notes' => $notes,
            ]);

            foreach ($returnItems as $item) {
                CustomerReturnItem::create([
                    'return_id' => $return->id,
                    'invoice_item_id' => $item['invoice_item_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                DB::table('main_stock')
                    ->where('product_id', $item['product_id'])
                    ->increment('quantity', $item['quantity']);
            }

            CustomerDebtLedger::create([
                'customer_id' => $customerId,
                'entry_type' => 'return',
                'return_id' => $return->id,
                'amount' => -$totalAmount,
            ]);

            return $return->load('items.product', 'invoice', 'customer');
        });
    }

    public function cancelReturn($returnId, $salesUserId)
    {
        return DB::transaction(function () use ($returnId, $salesUserId) {
            $return = CustomerReturn::where('id', $returnId)
                ->where('sales_user_id', $salesUserId)
                ->where('status', 'completed')
                ->firstOrFail();

            foreach ($return->items as $item) {
                DB::table('main_stock')
                    ->where('product_id', $item->product_id)
                    ->decrement('quantity', $item->quantity);
            }

            CustomerDebtLedger::create([
                'customer_id' => $return->customer_id,
                'entry_type' => 'return',
                'return_id' => $return->id,
                'amount' => $return->total_amount,
            ]);

            $return->update(['status' => 'cancelled']);
            return $return;
        });
    }

    private function generateReturnNumber()
    {
        return 'CR-' . date('Ymd') . '-' . str_pad(CustomerReturn::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
