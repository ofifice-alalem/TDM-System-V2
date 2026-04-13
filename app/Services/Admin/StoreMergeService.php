<?php

namespace App\Services\Admin;

use App\Models\Store;
use Illuminate\Support\Facades\DB;

class StoreMergeService
{
    public function merge(int $primaryId, int $duplicateId): void
    {
        if ($primaryId === $duplicateId) {
            throw new \Exception('لا يمكن دمج المتجر مع نفسه');
        }

        Store::findOrFail($primaryId);
        Store::findOrFail($duplicateId);

        DB::transaction(function () use ($primaryId, $duplicateId) {
            $tables = [
                'sales_invoices',
                'sales_returns',
                'store_payments',
                'store_pending_stock',
                'store_actual_stock',
                'store_return_pending_stock',
                'marketer_commissions',
                'store_debt_ledger',
            ];

            foreach ($tables as $table) {
                DB::table($table)->where('store_id', $duplicateId)->update(['store_id' => $primaryId]);
            }

            // إعادة حساب balance_after في store_debt_ledger بالترتيب الزمني
            $entries = DB::table('store_debt_ledger')
                ->where('store_id', $primaryId)
                ->orderBy('id')
                ->get();

            $balance = 0;
            foreach ($entries as $entry) {
                $balance += $entry->amount;
                DB::table('store_debt_ledger')
                    ->where('id', $entry->id)
                    ->update(['balance_after' => $balance]);
            }

            // دمج المخزون الفعلي (store_actual_stock) — جمع الكميات للمنتج نفسه
            $duplicateStock = DB::table('store_actual_stock')->where('store_id', $primaryId)->get();
            foreach ($duplicateStock as $row) {
                $existing = DB::table('store_actual_stock')
                    ->where('store_id', $primaryId)
                    ->where('product_id', $row->product_id)
                    ->count();
                if ($existing > 1) {
                    $total = DB::table('store_actual_stock')
                        ->where('store_id', $primaryId)
                        ->where('product_id', $row->product_id)
                        ->sum('quantity');
                    DB::table('store_actual_stock')
                        ->where('store_id', $primaryId)
                        ->where('product_id', $row->product_id)
                        ->delete();
                    DB::table('store_actual_stock')->insert([
                        'store_id'   => $primaryId,
                        'product_id' => $row->product_id,
                        'quantity'   => $total,
                    ]);
                }
            }

            DB::table('stores')->where('id', $duplicateId)->delete();
        });
    }
}
