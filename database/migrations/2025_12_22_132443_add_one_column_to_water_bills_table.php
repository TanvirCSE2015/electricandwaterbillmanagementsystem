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
        Schema::table('water_bills', function (Blueprint $table) {
            $table->foreignId('water_invoice_id')->after('water_customer_id')->nullable()->constrained('water_invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_bills', function (Blueprint $table) {
            $table->dropColumn('water_invoice_id');
        });
    }
};
