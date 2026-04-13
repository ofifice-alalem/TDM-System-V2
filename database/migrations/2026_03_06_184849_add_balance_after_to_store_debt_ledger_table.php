<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('store_debt_ledger', function (Blueprint $table) {
            $table->decimal('balance_after', 12, 2)->after('amount');
            $table->index(['store_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_debt_ledger', function (Blueprint $table) {
            $table->dropIndex(['store_id', 'id']);
            $table->dropColumn('balance_after');
        });
    }
};
