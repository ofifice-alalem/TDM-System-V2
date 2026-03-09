<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_debt_ledger', function (Blueprint $table) {
            $table->foreignId('marketer_id')->nullable()->after('store_id')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('store_debt_ledger', function (Blueprint $table) {
            $table->dropForeign(['marketer_id']);
            $table->dropColumn('marketer_id');
        });
    }
};