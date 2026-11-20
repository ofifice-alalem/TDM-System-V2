<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin', 'display_name' => 'مدير النظام', 'description' => 'صلاحيات كاملة', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'warehouse_keeper', 'display_name' => 'أمين المخزن', 'description' => 'إدارة المخزون', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'marketer', 'display_name' => 'مسوق', 'description' => 'البيع والتوزيع', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'sales', 'display_name' => 'مبيعات', 'description' => 'المبيعات المباشرة', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'super_admin', 'display_name' => 'Super Admin', 'description' => 'إدارة الميزات', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Users
        $this->call(UserSeeder::class);

        $this->call(FeatureSeeder::class);

        // Products
        DB::table('products')->insert([
            ['name' => 'منتج A', 'barcode' => 'PRD001', 'description' => 'وصف المنتج A', 'current_price' => 100.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'منتج B', 'barcode' => 'PRD002', 'description' => 'وصف المنتج B', 'current_price' => 50.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'منتج C', 'barcode' => 'PRD003', 'description' => 'وصف المنتج C', 'current_price' => 75.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'منتج D', 'barcode' => 'PRD004', 'description' => 'وصف المنتج D', 'current_price' => 120.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'منتج E', 'barcode' => 'PRD005', 'description' => 'وصف المنتج E', 'current_price' => 200.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Main Stock
        DB::table('main_stock')->insert([
            ['product_id' => 1, 'quantity' => 1000, 'updated_at' => now()],
            ['product_id' => 2, 'quantity' => 2000, 'updated_at' => now()],
            ['product_id' => 3, 'quantity' => 1500, 'updated_at' => now()],
            ['product_id' => 4, 'quantity' => 800, 'updated_at' => now()],
            ['product_id' => 5, 'quantity' => 500, 'updated_at' => now()],
        ]);
    }
}
