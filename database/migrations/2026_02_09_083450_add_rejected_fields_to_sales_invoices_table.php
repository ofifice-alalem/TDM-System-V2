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
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending')->change();
            $table->foreignId('rejected_by')->nullable()->after('keeper_id')->constrained('users');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['rejected_by', 'rejected_at']);
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending')->change();
        });
    }
};
