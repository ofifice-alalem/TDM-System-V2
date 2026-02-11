<?php

namespace App\Services\Warehouse;

use App\Models\FactoryInvoice;
use App\Models\FactoryInvoiceItem;
use App\Models\WarehouseStockLog;
use Illuminate\Support\Facades\DB;

class MainStockService
{
    public function createFactoryInvoice($keeperId, array $items, $notes = null)
    {
        return DB::transaction(function () use ($keeperId, $items, $notes) {
            $invoice = FactoryInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'keeper_id' => $keeperId,
                'status' => 'pending',
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                FactoryInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $invoice->load('items.product');
        });
    }

    public function documentInvoice($invoiceId, $keeperId, $stampedImage)
    {
        return DB::transaction(function () use ($invoiceId, $keeperId, $stampedImage) {
            $invoice = FactoryInvoice::where('id', $invoiceId)
                ->where('status', 'pending')
                ->firstOrFail();

            $this->addStockToMain($invoice);

            $invoice->update([
                'status' => 'documented',
                'documented_by' => $keeperId,
                'documented_at' => now(),
                'stamped_image' => $stampedImage,
            ]);

            WarehouseStockLog::create([
                'invoice_type' => 'factory',
                'invoice_id' => $invoice->id,
                'keeper_id' => $keeperId,
                'action' => 'add',
            ]);

            return $invoice;
        });
    }

    public function cancelInvoice($invoiceId, $keeperId, $reason)
    {
        return DB::transaction(function () use ($invoiceId, $keeperId, $reason) {
            $invoice = FactoryInvoice::where('id', $invoiceId)
                ->where('status', 'pending')
                ->firstOrFail();

            $invoice->update([
                'status' => 'cancelled',
                'cancelled_by' => $keeperId,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            return $invoice;
        });
    }

    private function addStockToMain($invoice)
    {
        foreach ($invoice->items as $item) {
            DB::table('main_stock')->updateOrInsert(
                ['product_id' => $item->product_id],
                ['quantity' => DB::raw("quantity + {$item->quantity}"), 'updated_at' => now()]
            );
        }
    }

    private function generateInvoiceNumber()
    {
        return 'FI-' . date('Ymd') . '-' . str_pad(FactoryInvoice::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
