<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure roles exist in database
        $roles = [
            ['id' => 1, 'name' => 'admin', 'guard_name' => 'web', 'display_name' => 'مدير النظام', 'description' => 'صلاحيات كاملة', 'is_active' => true],
            ['id' => 2, 'name' => 'warehouse_keeper', 'guard_name' => 'web', 'display_name' => 'أمين المخزن', 'description' => 'إدارة المخزون', 'is_active' => true],
            ['id' => 3, 'name' => 'marketer', 'guard_name' => 'web', 'display_name' => 'مسوق', 'description' => 'البيع والتوزيع', 'is_active' => true],
            ['id' => 4, 'name' => 'sales', 'guard_name' => 'web', 'display_name' => 'مبيعات', 'description' => 'المبيعات المباشرة', 'is_active' => true],
            ['id' => 5, 'name' => 'super_admin', 'guard_name' => 'web', 'display_name' => 'Super Admin', 'description' => 'إدارة الميزات', 'is_active' => true],
        ];

        foreach ($roles as $role) {
            $existing = DB::table('roles')->where('name', $role['name'])->first();
            if ($existing) {
                DB::table('roles')->where('id', $existing->id)->update([
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                    'is_active' => $role['is_active'],
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('roles')->updateOrInsert(
                    ['id' => $role['id']],
                    array_merge($role, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            }
        }

        // Map role names to IDs
        $roleMap = DB::table('roles')->pluck('id', 'name')->toArray();

        // 2. Ensure system user (id = 0) exists for system invoices/debts
        DB::statement('SET SESSION sql_mode="NO_AUTO_VALUE_ON_ZERO"');
        DB::table('users')->updateOrInsert(
            ['id' => 0],
            [
                'username' => 'system',
                'password_hash' => 'N/A',
                'full_name' => 'النظام',
                'role_id' => $roleMap['marketer'] ?? 3,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Default users for each role in the system
        $users = [
            [
                'username' => 'admin',
                'password_hash' => Hash::make('password'),
                'full_name' => 'المدير العام',
                'role_id' => $roleMap['admin'] ?? 1,
                'commission_rate' => null,
                'phone' => '0500000000',
                'is_active' => true,
            ],
            [
                'username' => 'superadmin',
                'password_hash' => Hash::make('password'),
                'full_name' => 'المدير الفائق (Super Admin)',
                'role_id' => $roleMap['super_admin'] ?? 5,
                'commission_rate' => null,
                'phone' => '0500000005',
                'is_active' => true,
            ],
            [
                'username' => 'keeper1',
                'password_hash' => Hash::make('password'),
                'full_name' => 'أحمد أمين المخزن',
                'role_id' => $roleMap['warehouse_keeper'] ?? 2,
                'commission_rate' => null,
                'phone' => '0500000001',
                'is_active' => true,
            ],
            [
                'username' => 'marketer1',
                'password_hash' => Hash::make('password'),
                'full_name' => 'محمد المسوق',
                'role_id' => $roleMap['marketer'] ?? 3,
                'commission_rate' => 5.00,
                'phone' => '0500000002',
                'is_active' => true,
            ],
            [
                'username' => 'marketer2',
                'password_hash' => Hash::make('password'),
                'full_name' => 'خالد المسوق',
                'role_id' => $roleMap['marketer'] ?? 3,
                'commission_rate' => 3.50,
                'phone' => '0500000003',
                'is_active' => true,
            ],
            [
                'username' => 'sales1',
                'password_hash' => Hash::make('password'),
                'full_name' => 'علي المبيعات',
                'role_id' => $roleMap['sales'] ?? 4,
                'commission_rate' => null,
                'phone' => '0500000004',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }
    }
}
