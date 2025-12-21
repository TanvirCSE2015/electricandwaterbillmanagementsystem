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
        Schema::create('water_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_customer_id');
            $table->integer('water_bill_month');
            $table->integer('water_bill_year'); 
            $table->float('base_amount', 10, 2);
            $table->float('surcharge_percent', 5, 2)->default(0);
            $table->float('surcharge_amount', 10, 2)->default(0);
            $table->float('total_amount', 10, 2);
            $table->float('paid_amount', 10, 2);
            $table->date('bill_creation_date');
            $table->date('bill_due_date');
            $table->boolean('is_paid')->default(false);
            $table->date('paid_at')->nullable();
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
        Schema::dropIfExists('water_bills');
    }
};
