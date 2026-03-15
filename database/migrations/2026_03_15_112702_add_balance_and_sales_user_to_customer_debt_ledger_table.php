<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_debt_ledger', function (Blueprint $table) {
            $table->decimal('balance_after', 12, 2)->default(0)->after('amount');
            $table->foreignId('sales_user_id')->nullable()->after('balance_after')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customer_debt_ledger', function (Blueprint $table) {
            $table->dropForeign(['sales_user_id']);
            $table->dropColumn(['balance_after', 'sales_user_id']);
        });
    }
};
