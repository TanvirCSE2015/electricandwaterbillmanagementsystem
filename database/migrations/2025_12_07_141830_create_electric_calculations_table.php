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
        Schema::create('electric_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elecric_area_id');
            $table->integer('bill_month');
            $table->integer('bill_year');
            $table->float('cantral_ac_rate');
            $table->float('common_area_rate');
            $table->float('water_area_rate');
            $table->date('due_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_calculations');
    }
};
