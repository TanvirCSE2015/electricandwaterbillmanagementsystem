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
        Schema::create('water_settings', function (Blueprint $table) {
            $table->id();
            $table->float('monthly_rate',8,2);
            $table->float('monthly_const_rate',8,2);
            $table->float('monthly_surcharge',8,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_settings');
    }
};
