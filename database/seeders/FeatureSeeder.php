<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['key' => 'admin.customer-merge',   'label' => 'دمج العملاء',       'role' => 'admin'],
            ['key' => 'admin.store-merge',       'label' => 'دمج المتاجر',       'role' => 'admin'],
            ['key' => 'admin.combined-summary',  'label' => 'الملخص الشامل',     'role' => 'admin'],
            ['key' => 'admin.products-pricing',  'label' => 'تسعير المنتجات',    'role' => 'admin'],
            ['key' => 'admin.staff-pricing',     'label' => 'معدل الموظفين',     'role' => 'admin'],
        ];

        foreach ($features as $feature) {
            DB::table('features')->insertOrIgnore(
                array_merge($feature, [
                    'is_enabled' => true,
                    'mode'       => 'permanent',
                    'starts_at'  => null,
                    'ends_at'    => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
