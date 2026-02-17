<?php

namespace App\Services\Sales;

use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceItem;
use App\Models\CustomerDebtLedger;
use App\Models\CustomerPayment;
use Illuminate\Support\Facades\DB;

class CustomerInvoiceService
{
    public function createInvoice($salesUserId, $customerId, array $items, $discountAmount, $paidAmount = 0, $paymentMethod = 'cash', $notes = null)
    {
        return DB::transaction(function () use ($salesUserId, $customerId, $items, $discountAmount, $paidAmount, $paymentMethod, $notes) {
            $this->checkMainStock($items);

            $subtotal = 0;
            $invoiceItems = [];

            foreach ($items as $item) {
                $product = DB::table('products')->find($item['product_id']);
                $quantity = $item['quantity'];
                $unitPrice = $product->customer_price ?? $product->current_price;
                $totalPrice = $quantity * $unitPrice;
                $subtotal += $totalPrice;

                $invoiceItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            $totalAmount = $subtotal - $discountAmount;
            
            // Determine payment type
            if ($paidAmount >= $totalAmount) {
                $paymentType = 'cash';
            } elseif ($paidAmount > 0) {
                $paymentType = 'partial';
            } else {
                $paymentType = 'credit';
            }

            $invoice = CustomerInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $customerId,
                'sales_user_id' => $salesUserId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_type' => $paymentType,
                'status' => 'completed',
                'notes' => $notes,
            ]);

            foreach ($invoiceItems as $item) {
                CustomerInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                DB::table('main_stock')
                    ->where('product_id', $item['product_id'])
                    ->decrement('quantity', $item['quantity']);
            }

            // Record sale in debt ledger
            CustomerDebtLedger::create([
                'customer_id' => $customerId,
                'entry_type' => 'sale',
                'invoice_id' => $invoice->id,
                'amount' => $totalAmount,
            ]);

            // Create payment if amount paid
            if ($paidAmount > 0) {
                $this->createPayment($salesUserId, $customerId, $paidAmount, $paymentMethod);
            }

            return $invoice->load('items.product', 'customer');
        });
    }

    public function cancelInvoice($invoiceId, $salesUserId)
    {
        return DB::transaction(function () use ($invoiceId, $salesUserId) {
            $invoice = CustomerInvoice::where('id', $invoiceId)
                ->where('sales_user_id', $salesUserId)
                ->where('status', 'completed')
                ->firstOrFail();

            foreach ($invoice->items as $item) {
                DB::table('main_stock')
                    ->where('product_id', $item->product_id)
                    ->increment('quantity', $item->quantity);
            }

            CustomerDebtLedger::create([
                'customer_id' => $invoice->customer_id,
                'entry_type' => 'sale',
                'invoice_id' => $invoice->id,
                'amount' => -$invoice->total_amount,
            ]);

            $invoice->update(['status' => 'cancelled']);
            return $invoice;
        });
    }

    private function createPayment($salesUserId, $customerId, $amount, $paymentMethod)
    {
        $payment = CustomerPayment::create([
            'payment_number' => $this->generatePaymentNumber(),
            'customer_id' => $customerId,
            'sales_user_id' => $salesUserId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
        ]);

        CustomerDebtLedger::create([
            'customer_id' => $customerId,
            'entry_type' => 'payment',
            'payment_id' => $payment->id,
            'amount' => -$amount,
        ]);

        return $payment;
    }

    private function checkMainStock($items)
    {
        foreach ($items as $item) {
            $product = DB::table('products')->find($item['product_id']);
            $available = DB::table('main_stock')
                ->where('product_id', $item['product_id'])
                ->value('quantity') ?? 0;

            if ($available < $item['quantity']) {
                throw new \Exception("المنتج {$product->name} غير متوفر بالكمية المطلوبة (متوفر: {$available})");
            }
        }
    }

    private function generateInvoiceNumber()
    {
        return 'CI-' . date('Ymd') . '-' . str_pad(CustomerInvoice::count() + 1, 5, '0', STR_PAD_LEFT);
    }

    private function generatePaymentNumber()
    {
        return 'CP-' . date('Ymd') . '-' . str_pad(CustomerPayment::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
