<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factory_invoices', function (Blueprint $table) {
            $table->renameColumn('keeper_id', 'created_by');
        });
    }

    public function down(): void
    {
        Schema::table('factory_invoices', function (Blueprint $table) {
            $table->renameColumn('created_by', 'keeper_id');
        });
    }
};
