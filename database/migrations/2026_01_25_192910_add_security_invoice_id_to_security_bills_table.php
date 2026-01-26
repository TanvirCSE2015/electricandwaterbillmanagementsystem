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
        Schema::table('security_bills', function (Blueprint $table) {
            $table->foreignId('security_invoice_id')->after('water_customer_id')->nullable()->constrained('security_invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('security_bills', function (Blueprint $table) {
            $table->dropForeign(['security_invoice_id']);
            $table->dropColumn('security_invoice_id');
        });
    }
};
