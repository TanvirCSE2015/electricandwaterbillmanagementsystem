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
        Schema::create('security_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_customer_id')->constrained('water_customers')->onDelete('cascade');
            $table->string('s_invoice_number')->unique();
            $table->date('s_invoice_date');
            $table->integer('s_invoice_month');
            $table->string('s_invoice_month_name');
            $table->integer('s_invoice_year');
            $table->string('s_from_month');
            $table->string('s_to_month');
            $table->decimal('s_total_amount', 10, 2);
            $table->string('s_pay_name')->nullable();
            $table->string('s_pay_number')->nullable();
            $table->string('s_pay_method')->nullable();
            $table->foreignId('s_created_by')->constrained('users')->onDelete('cascade')->nullable();
            $table->text('s_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_invoices');
    }
};
