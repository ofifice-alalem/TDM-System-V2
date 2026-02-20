<?php

namespace App\Services\Marketer;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\ProductPromotion;
use App\Models\InvoiceDiscountTier;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function createInvoice($marketerId, $storeId, array $items, $notes = null)
    {
        return DB::transaction(function () use ($marketerId, $storeId, $items, $notes) {
            $this->checkMarketerStock($marketerId, $items);

            $subtotal = 0;
            $productDiscount = 0;
            $invoiceItems = [];

            foreach ($items as $item) {
                $product = DB::table('products')->find($item['product_id']);
                $quantity = $item['quantity'];
                $unitPrice = $product->current_price;
                
                $promotion = $this->getActivePromotion($item['product_id']);
                $freeQuantity = 0;
                
                if ($promotion && $quantity >= $promotion->min_quantity) {
                    $times = floor($quantity / $promotion->min_quantity);
                    $freeQuantity = $times * $promotion->free_quantity;
                    $productDiscount += $freeQuantity * $unitPrice;
                }

                $totalPrice = $quantity * $unitPrice;
                $subtotal += $totalPrice + ($freeQuantity * $unitPrice);

                $invoiceItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'free_quantity' => $freeQuantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'promotion_id' => $promotion?->id,
                    'total_quantity' => $quantity + $freeQuantity
                ];
            }

            $invoiceDiscount = $this->calculateInvoiceDiscount($subtotal - $productDiscount);
            $totalAmount = $subtotal - $productDiscount - $invoiceDiscount['amount'];

            $invoice = SalesInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'marketer_id' => $marketerId,
                'store_id' => $storeId,
                'subtotal' => $subtotal,
                'product_discount' => $productDiscount,
                'invoice_discount_type' => $invoiceDiscount['type'],
                'invoice_discount_value' => $invoiceDiscount['value'],
                'invoice_discount_amount' => $invoiceDiscount['amount'],
                'invoice_discount_tier_id' => $invoiceDiscount['tier_id'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $notes,
            ]);

            foreach ($invoiceItems as $item) {
                SalesInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'free_quantity' => $item['free_quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'promotion_id' => $item['promotion_id'],
                ]);

                DB::table('marketer_actual_stock')
                    ->where('marketer_id', $marketerId)
                    ->where('product_id', $item['product_id'])
                    ->decrement('quantity', $item['total_quantity']);

                DB::table('store_pending_stock')->insert([
                    'store_id' => $storeId,
                    'sales_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['total_quantity'],
                    'created_at' => now(),
                ]);
            }

            return $invoice->load('items.product', 'store');
        });
    }

    public function cancelInvoice($invoiceId, $marketerId)
    {
        return DB::transaction(function () use ($invoiceId, $marketerId) {
            $invoice = SalesInvoice::where('id', $invoiceId)
                ->where('marketer_id', $marketerId)
                ->where('status', 'pending')
                ->firstOrFail();

            foreach ($invoice->items as $item) {
                $totalQuantity = $item->quantity + $item->free_quantity;

                DB::table('marketer_actual_stock')
                    ->where('marketer_id', $marketerId)
                    ->where('product_id', $item->product_id)
                    ->increment('quantity', $totalQuantity);

                DB::table('store_pending_stock')
                    ->where('sales_invoice_id', $invoice->id)
                    ->where('product_id', $item->product_id)
                    ->delete();
            }

            $invoice->update(['status' => 'cancelled']);
            return $invoice;
        });
    }

    private function checkMarketerStock($marketerId, $items)
    {
        foreach ($items as $item) {
            $product = DB::table('products')->find($item['product_id']);
            $available = DB::table('marketer_actual_stock')
                ->where('marketer_id', $marketerId)
                ->where('product_id', $item['product_id'])
                ->value('quantity') ?? 0;

            $promotion = $this->getActivePromotion($item['product_id']);
            $freeQuantity = 0;
            if ($promotion && $item['quantity'] >= $promotion->min_quantity) {
                $times = floor($item['quantity'] / $promotion->min_quantity);
                $freeQuantity = $times * $promotion->free_quantity;
            }

            $totalNeeded = $item['quantity'] + $freeQuantity;

            if ($available < $totalNeeded) {
                throw new \Exception("المنتج {$product->name} غير متوفر بالكمية المطلوبة (متوفر: {$available})");
            }
        }
    }

    private function getActivePromotion($productId)
    {
        return ProductPromotion::where('product_id', $productId)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    private function calculateInvoiceDiscount($subtotal)
    {
        $tier = InvoiceDiscountTier::where('min_amount', '<=', $subtotal)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('min_amount', 'desc')
            ->first();

        if (!$tier) {
            return ['type' => null, 'value' => null, 'amount' => 0, 'tier_id' => null];
        }

        $amount = $tier->discount_type === 'percentage'
            ? $subtotal * ($tier->discount_percentage / 100)
            : $tier->discount_amount;

        return [
            'type' => $tier->discount_type,
            'value' => $tier->discount_type === 'percentage' ? $tier->discount_percentage : $tier->discount_amount,
            'amount' => $amount,
            'tier_id' => $tier->id
        ];
    }

    private function generateInvoiceNumber()
    {
        return 'SI-' . date('Ymd') . '-' . str_pad(SalesInvoice::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
