<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factory_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('keeper_id')->constrained('users');
            $table->enum('status', ['pending', 'documented'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('documented_by')->nullable()->constrained('users');
            $table->timestamp('documented_at')->nullable();
            $table->string('stamped_image')->nullable();
            $table->timestamps();
        });

        Schema::create('factory_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('factory_invoices')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factory_invoice_items');
        Schema::dropIfExists('factory_invoices');
    }
};
