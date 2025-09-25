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
        Schema::create('electric_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meter_reading_id')->constrained()->cascadeOnDelete();
            $table->foreignId('electric_bill_setting_id')->constrained()->cascadeOnDelete();
            $table->date('bill_date');
            $table->integer('billing_month');
            $table->integer('billing_year');
            $table->string('bill_month_name');
            $table->unsignedInteger('consumed_units');
            $table->integer('system_loss_units');
            $table->float('base_amount', 8, 2);
            $table->float('demand_charge', 8, 2);
            $table->float('service_charge', 8, 2);
            $table->float('surcharge', 8, 2);
            $table->float('vat', 8, 2);
            $table->float('total_amount', 8, 2);
            $table->boolean('is_paid')->default(false);
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('paid_by')->constrained('users')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_bills');
    }
};
