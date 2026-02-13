<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LargeDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        echo "Creating marketers...\n";
        $marketerIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $marketerIds[] = DB::table('users')->insertGetId([
                'username' => "marketer{$i}",
                'password_hash' => bcrypt('password'),
                'full_name' => "مسوق {$i}",
                'role_id' => 3,
                'commission_rate' => rand(5, 15),
                'phone' => '07' . rand(700000000, 799999999),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "Creating stores...\n";
        $storeIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $storeIds[] = DB::table('stores')->insertGetId([
                'name' => "متجر {$i}",
                'owner_name' => "صاحب متجر {$i}",
                'phone' => '07' . rand(700000000, 799999999),
                'location' => "موقع {$i}",
                'address' => "عنوان متجر {$i}",
                'is_active' => true,
                'created_at' => now(),
            ]);
        }

        $statuses = ['pending', 'approved', 'cancelled'];
        $startDate = Carbon::now()->subMonths(6);
        
        echo "Creating sales invoices (this will take a while)...\n";
        $batchSize = 1000;
        $totalInvoices = 10000;
        
        for ($batch = 0; $batch < $totalInvoices / $batchSize; $batch++) {
            $invoices = [];
            $invoiceItems = [];
            
            for ($i = 0; $i < $batchSize; $i++) {
                $invoiceNum = ($batch * $batchSize) + $i + 1;
                $marketerId = $marketerIds[array_rand($marketerIds)];
                $storeId = $storeIds[array_rand($storeIds)];
                $status = $statuses[array_rand($statuses)];
                $createdAt = $startDate->copy()->addMinutes(rand(0, 259200));
                
                $subtotal = 0;
                $productCount = rand(3, 8);
                $tempItems = [];
                
                for ($p = 0; $p < $productCount; $p++) {
                    $productId = rand(1, 20);
                    $quantity = rand(1, 50);
                    $unitPrice = rand(500, 5000) / 100;
                    $totalPrice = $quantity * $unitPrice;
                    $subtotal += $totalPrice;
                    
                    $tempItems[] = [
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ];
                }
                
                $totalAmount = $subtotal;
                
                $invoiceId = DB::table('sales_invoices')->insertGetId([
                    'invoice_number' => 'INV-' . str_pad($invoiceNum, 6, '0', STR_PAD_LEFT),
                    'marketer_id' => $marketerId,
                    'store_id' => $storeId,
                    'total_amount' => $totalAmount,
                    'subtotal' => $subtotal,
                    'product_discount' => 0,
                    'invoice_discount_amount' => 0,
                    'status' => $status,
                    'keeper_id' => $status === 'approved' ? 2 : null,
                    'confirmed_at' => $status === 'approved' ? $createdAt : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                
                foreach ($tempItems as $item) {
                    $invoiceItems[] = [
                        'invoice_id' => $invoiceId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'free_quantity' => 0,
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'promotion_id' => null,
                    ];
                }
            }
            
            if (!empty($invoiceItems)) {
                DB::table('sales_invoice_items')->insert($invoiceItems);
            }
            
            echo "Batch " . ($batch + 1) . " completed (" . (($batch + 1) * $batchSize) . " invoices)\n";
        }

        echo "Creating store payments...\n";
        $paymentStatuses = ['pending', 'approved', 'rejected', 'cancelled'];
        for ($i = 1; $i <= 5000; $i++) {
            $status = $paymentStatuses[array_rand($paymentStatuses)];
            $marketerId = $marketerIds[array_rand($marketerIds)];
            $storeId = $storeIds[array_rand($storeIds)];
            $amount = rand(10000, 500000) / 100;
            $createdAt = $startDate->copy()->addMinutes(rand(0, 259200));
            
            $paymentId = DB::table('store_payments')->insertGetId([
                'payment_number' => 'PAY-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'store_id' => $storeId,
                'marketer_id' => $marketerId,
                'keeper_id' => 2,
                'amount' => $amount,
                'payment_method' => ['cash', 'transfer', 'certified_check'][array_rand(['cash', 'transfer', 'certified_check'])],
                'status' => $status,
                'confirmed_at' => $status === 'approved' ? $createdAt : null,
                'created_at' => $createdAt,
            ]);
            
            // Create commission record for approved payments
            if ($status === 'approved') {
                $marketer = DB::table('users')->where('id', $marketerId)->first();
                $commissionRate = $marketer->commission_rate ?? rand(5, 15);
                $commissionAmount = $amount * ($commissionRate / 100);
                
                DB::table('marketer_commissions')->insert([
                    'marketer_id' => $marketerId,
                    'store_id' => $storeId,
                    'keeper_id' => 2,
                    'payment_amount' => $amount,
                    'payment_id' => $paymentId,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'created_at' => $createdAt,
                ]);
            }
            
            if ($i % 500 == 0) {
                echo "Created {$i} payments\n";
            }
        }

        echo "Creating sales returns...\n";
        $returnStatuses = ['pending', 'approved', 'rejected', 'cancelled'];
        for ($i = 1; $i <= 3000; $i++) {
            $status = $returnStatuses[array_rand($returnStatuses)];
            $invoiceId = rand(1, 10000);
            $invoice = DB::table('sales_invoices')->where('id', $invoiceId)->first();
            
            if (!$invoice) continue;
            
            $createdAt = $startDate->copy()->addMinutes(rand(0, 259200));
            
            $returnId = DB::table('sales_returns')->insertGetId([
                'return_number' => 'RET-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'sales_invoice_id' => $invoiceId,
                'store_id' => $invoice->store_id,
                'marketer_id' => $invoice->marketer_id,
                'total_amount' => rand(5000, 50000) / 100,
                'status' => $status,
                'keeper_id' => $status === 'approved' ? 2 : null,
                'confirmed_at' => $status === 'approved' ? $createdAt : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            
            $invoiceItems = DB::table('sales_invoice_items')->where('invoice_id', $invoiceId)->limit(rand(1, 3))->get();
            foreach ($invoiceItems as $item) {
                DB::table('sales_return_items')->insert([
                    'return_id' => $returnId,
                    'sales_invoice_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => rand(1, $item->quantity),
                    'unit_price' => $item->unit_price,
                ]);
            }
            
            if ($i % 300 == 0) {
                echo "Created {$i} returns\n";
            }
        }

        echo "Creating marketer withdrawal requests...\n";
        $withdrawalStatuses = ['pending', 'approved', 'rejected', 'cancelled'];
        for ($i = 1; $i <= 2000; $i++) {
            $status = $withdrawalStatuses[array_rand($withdrawalStatuses)];
            $createdAt = $startDate->copy()->addMinutes(rand(0, 259200));
            
            DB::table('marketer_withdrawal_requests')->insert([
                'marketer_id' => $marketerIds[array_rand($marketerIds)],
                'requested_amount' => rand(10000, 200000) / 100,
                'status' => $status,
                'approved_by' => $status === 'approved' ? 1 : null,
                'approved_at' => $status === 'approved' ? $createdAt : null,
                'rejected_by' => $status === 'rejected' ? 1 : null,
                'rejected_at' => $status === 'rejected' ? $createdAt : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            
            if ($i % 200 == 0) {
                echo "Created {$i} withdrawal requests\n";
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        echo "Done!\n";
    }
}
