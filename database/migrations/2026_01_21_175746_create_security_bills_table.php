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
        Schema::create('security_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_customer_id')->constrained('water_customers')->onDelete('cascade');
            $table->integer('s_bill_month');
            $table->integer('s_bill_year');
            $table->string('s_flat_numbers')->nullable();
            $table->integer('s_total_flats');
            $table->float('base_amount',8,2);
            $table->float('total_amount',8,2);
            $table->float('paid_amount',8,2)->default(0);
            $table->date('bill_creation_date');
            $table->date('bill_due_date');
            $table->boolean('is_paid')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('s_invoice_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_bills');
    }
};
