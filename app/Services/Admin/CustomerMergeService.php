<?php

namespace App\Services\Admin;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerMergeService
{
    public function merge(int $primaryId, int $duplicateId): void
    {
        if ($primaryId === $duplicateId) {
            throw new \Exception('لا يمكن دمج العميل مع نفسه');
        }

        $primary   = Customer::findOrFail($primaryId);
        $duplicate = Customer::findOrFail($duplicateId);

        DB::transaction(function () use ($primary, $duplicate) {
            // نقل البيانات
            DB::table('customer_invoices') ->where('customer_id', $duplicate->id)->update(['customer_id' => $primary->id]);
            DB::table('customer_payments') ->where('customer_id', $duplicate->id)->update(['customer_id' => $primary->id]);
            DB::table('customer_returns')  ->where('customer_id', $duplicate->id)->update(['customer_id' => $primary->id]);
            DB::table('customer_debt_ledger')->where('customer_id', $duplicate->id)->update(['customer_id' => $primary->id]);

            // إعادة حساب balance_after بالترتيب الزمني
            $entries = DB::table('customer_debt_ledger')
                ->where('customer_id', $primary->id)
                ->orderBy('id')
                ->get();

            $balance = 0;
            foreach ($entries as $entry) {
                $balance += $entry->amount;
                DB::table('customer_debt_ledger')
                    ->where('id', $entry->id)
                    ->update(['balance_after' => $balance]);
            }

            // حذف المكرر
            $duplicate->delete();
        });
    }
}
