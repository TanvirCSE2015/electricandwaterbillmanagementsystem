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
        Schema::table('electric_invoices', function (Blueprint $table) {
            $table->enum('due_type', ['previous_due', 'current_due', 'both'])->default('current_due')->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('electric_invoices', function (Blueprint $table) {
            $table->dropColumn('due_type');
        });
    }
};
