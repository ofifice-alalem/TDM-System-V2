<?php

namespace App\Services\Admin;

use App\Models\SalesInvoice;
use App\Models\StoreDebtLedger;
use Illuminate\Support\Facades\DB;

class OldDebtService
{
    public function create($storeId, $amount, $notes = null)
    {
        return DB::transaction(function () use ($storeId, $amount, $notes) {
            $invoice = SalesInvoice::create([
                'invoice_number'  => $this->generateInvoiceNumber(),
                'marketer_id'     => 0,
                'store_id'        => $storeId,
                'subtotal'        => $amount,
                'total_amount'    => $amount,
                'product_discount'        => 0,
                'invoice_discount_amount' => 0,
                'status'          => 'approved',
                'confirmed_at'    => now(),
                'notes'           => $notes,
            ]);

            $lastBalance = StoreDebtLedger::where('store_id', $storeId)
                ->latest('id')
                ->value('balance_after') ?? 0;

            StoreDebtLedger::create([
                'store_id'         => $storeId,
                'entry_type'       => 'sale',
                'sales_invoice_id' => $invoice->id,
                'amount'           => $amount,
                'balance_after'    => $lastBalance + $amount,
                'marketer_id'      => 0,
                'created_at'       => now(),
            ]);

            return $invoice;
        });
    }

    public function update(SalesInvoice $invoice, $amount, $notes = null)
    {
        return DB::transaction(function () use ($invoice, $amount, $notes) {
            $diff = $amount - $invoice->total_amount;

            $invoice->update([
                'subtotal'     => $amount,
                'total_amount' => $amount,
                'notes'        => $notes,
            ]);

            $ledger = StoreDebtLedger::where('sales_invoice_id', $invoice->id)->first();
            if ($ledger) {
                $ledger->update([
                    'amount'        => $amount,
                    'balance_after' => $ledger->balance_after + $diff,
                ]);
            }

            return $invoice;
        });
    }

    public function delete(SalesInvoice $invoice)
    {
        return DB::transaction(function () use ($invoice) {
            $ledger = StoreDebtLedger::where('sales_invoice_id', $invoice->id)->first();
            if ($ledger) {
                $ledger->delete();
            }
            $invoice->delete();
        });
    }

    private function generateInvoiceNumber(): string
    {
        $count = SalesInvoice::where('invoice_number', 'like', 'INV-OLD-%')->count();
        return 'INV-OLD-' . date('Ymd') . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
}
