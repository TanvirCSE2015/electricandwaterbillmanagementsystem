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
        Schema::table('electric_bills', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('is_paid');
            $table->foreignId('electric_invoice_id')->nullable()->after('is_paid')->constrained('electric_invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('electric_bills', function (Blueprint $table) {
            //
        });
    }
};
