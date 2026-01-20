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
        Schema::table('water_bills', function (Blueprint $table) {
            $table->string('flat_numbers')->after('water_bill_year')->nullable();
            $table->integer('total_flats')->after('flat_numbers')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_bills', function (Blueprint $table) {
            //
        });
    }
};
