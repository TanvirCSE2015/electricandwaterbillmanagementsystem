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
        Schema::table('water_invoices', function (Blueprint $table) {
           
            $table->string('w_transaction_number')->after('w_pay_method')->nullable();
            $table->enum('w_due_type', ['previous', 'current','both'])->after('w_created_by')->nullable();
        });
    }
     
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_invoices', function (Blueprint $table) {
            $table->dropColumn(['w_pay_name', 'w_pay_number', 'w_pay_method','w_transaction_number', 'w_due_type']);
        });
    }
};
