<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_debt_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete()->cascadeOnUpdate();
            $table->enum('entry_type', ['sale', 'payment', 'return']);
            $table->foreignId('invoice_id')->nullable()->constrained('customer_invoices')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('return_id')->nullable()->constrained('customer_returns')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('payment_id')->nullable()->constrained('customer_payments')->nullOnDelete()->cascadeOnUpdate();
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->index('customer_id');
            $table->index('entry_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_debt_ledger');
    }
};
