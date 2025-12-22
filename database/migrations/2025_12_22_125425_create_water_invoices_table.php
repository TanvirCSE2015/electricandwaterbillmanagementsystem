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
        Schema::create('water_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_customer_id')->constrained('water_customers')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('w_invoice_date');
            $table->integer('w_invoice_month');
            $table->string('w_invoice_month_name');
            $table->integer('w_invoice_year');
            $table->string('w_from_month');
            $table->string('w_to_month');
            $table->decimal('w_total_amount', 10, 2);
            $table->string('w_pay_name')->nullable();
            $table->string('w_pay_number')->nullable();
            $table->string('w_pay_method')->nullable();
            $table->foreignId('w_created_by')->constrained('users')->onDelete('cascade')->nullable();
            $table->text('w_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_invoices');
    }
};
