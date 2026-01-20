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
        Schema::create('water_customer_flats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_customer_id')->constrained('water_customers')->onDelete('cascade');
            $table->string('flat_number');
            $table->boolean('is_occupied')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_customer_flats');
    }
};
