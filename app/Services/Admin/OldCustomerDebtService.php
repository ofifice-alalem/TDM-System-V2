<?php

namespace App\Services\Admin;

use App\Models\CustomerInvoice;
use App\Models\CustomerDebtLedger;
use Illuminate\Support\Facades\DB;

class OldCustomerDebtService
{
    public function create($customerId, $amount, $notes = null)
    {
        return DB::transaction(function () use ($customerId, $amount, $notes) {
            $invoice = CustomerInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id'    => $customerId,
                'sales_user_id'  => 0,
                'subtotal'       => $amount,
                'discount_amount'=> 0,
                'total_amount'   => $amount,
                'payment_type'   => 'credit',
                'status'         => 'completed',
                'confirmed_at'   => now(),
                'notes'          => $notes,
            ]);

            $lastBalance = CustomerDebtLedger::where('customer_id', $customerId)
                ->latest('id')
                ->value('balance_after') ?? 0;

            CustomerDebtLedger::create([
                'customer_id'   => $customerId,
                'entry_type'    => 'sale',
                'invoice_id'    => $invoice->id,
                'amount'        => $amount,
                'balance_after' => $lastBalance + $amount,
                'sales_user_id' => 0,
            ]);

            return $invoice;
        });
    }

    public function update(CustomerInvoice $invoice, $amount, $notes = null)
    {
        return DB::transaction(function () use ($invoice, $amount, $notes) {
            $oldAmount = $invoice->total_amount;
            $diff      = $amount - $oldAmount;

            $invoice->update([
                'subtotal'     => $amount,
                'total_amount' => $amount,
                'notes'        => $notes,
            ]);

            $ledger = CustomerDebtLedger::where('invoice_id', $invoice->id)->first()
                ?? CustomerDebtLedger::where('customer_id', $invoice->customer_id)
                    ->where('amount', $oldAmount)
                    ->whereNull('invoice_id')
                    ->first();

            if ($ledger) {
                $ledger->update([
                    'invoice_id'    => $invoice->id,
                    'amount'        => $amount,
                    'balance_after' => $ledger->balance_after + $diff,
                ]);

                // تعديل balance_after لكل السجلات اللاحقة لنفس العميل
                DB::table('customer_debt_ledger')
                    ->where('customer_id', $invoice->customer_id)
                    ->where('id', '>', $ledger->id)
                    ->update(['balance_after' => DB::raw("balance_after + {$diff}")]);
            }

            return $invoice;
        });
    }

    public function delete(CustomerInvoice $invoice)
    {
        return DB::transaction(function () use ($invoice) {
            CustomerDebtLedger::where('invoice_id', $invoice->id)->delete();
            $invoice->delete();
        });
    }

    private function generateInvoiceNumber(): string
    {
        $count = CustomerInvoice::where('invoice_number', 'like', 'CI-OLD-%')->count();
        return 'CI-OLD-' . date('Ymd') . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
}
