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
        Schema::create('electric_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->integer('invoice_month');
            $table->string('invoice_month_name');
            $table->integer('invoice_year');
            $table->string('from_month');
            $table->string('to_month');
            $table->decimal('total_amount', 10, 2);
            $table->string('pay_name')->nullable();
            $table->string('pay_number')->nullable();
            $table->string('pay_method')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_invoices');
    }
};
