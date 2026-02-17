<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->insert([
            'id' => 4,
            'name' => 'sales',
            'display_name' => 'موظف المبيعات',
            'description' => 'إدارة المبيعات المباشرة للعملاء',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('roles')->where('id', 4)->delete();
    }
};
