<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('marketer_id')->constrained('users');
            $table->foreignId('store_id')->constrained('stores');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('product_discount', 12, 2)->default(0);
            $table->enum('invoice_discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('invoice_discount_value', 10, 2)->nullable();
            $table->decimal('invoice_discount_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');
            $table->foreignId('keeper_id')->nullable()->constrained('users');
            $table->string('stamped_invoice_image')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
