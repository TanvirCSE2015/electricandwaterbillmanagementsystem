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
        Schema::table('customers', function (Blueprint $table) {
            $table->float('shop_area',8,2)->after('address')->default(0);
            $table->float('central_ac_area',8,2)->after('shop_area')->default(0);
            $table->float('common_ac_area',8,2)->after('central_ac_area')->default(0);
            $table->float('water_area',8,2)->after('common_ac_area')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
};
