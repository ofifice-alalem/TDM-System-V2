<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factory_invoices', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('documented_at');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancelled_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_by');
            
            $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
        });
        
        DB::statement("ALTER TABLE factory_invoices MODIFY COLUMN status ENUM('pending', 'documented', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('factory_invoices', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['cancelled_at', 'cancelled_by', 'cancellation_reason']);
        });
        
        DB::statement("ALTER TABLE factory_invoices MODIFY COLUMN status ENUM('pending', 'documented') NOT NULL DEFAULT 'pending'");
    }
};
