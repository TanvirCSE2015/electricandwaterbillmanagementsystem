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
        Schema::table('water_customers', function (Blueprint $table) {
            $table->double('s_previous_due', 10, 2)->after('previous_due')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_customers', function (Blueprint $table) {
            //
        });
    }
};
