<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customer_invoices', 'confirmed_at')) {
            Schema::table('customer_invoices', function (Blueprint $table) {
                $table->timestamp('confirmed_at')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customer_invoices', 'confirmed_at')) {
            Schema::table('customer_invoices', function (Blueprint $table) {
                $table->dropColumn('confirmed_at');
            });
        }
    }
};
