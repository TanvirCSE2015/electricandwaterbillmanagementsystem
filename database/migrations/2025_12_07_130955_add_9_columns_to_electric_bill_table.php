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
        Schema::table('electric_bills', function (Blueprint $table) {
            $table->float('unit_rate_ac',8,2)->after('bill_month_name')->default(0);
            $table->float('unit_rate_common',8,2)->after('unit_rate_ac')->default(0);
            $table->float('unit_rate_water',8,2)->after('unit_rate_common')->default(0);
            $table->float('unit_ac',8,2)->after('consumed_units')->default(0);
            $table->float('unit_common',8,2)->after('unit_ac')->default(0);
            $table->float('unit_total',8,2)->after('system_loss_units');
            $table->float('ac_amount',8,2)->after('system_loss_units')->default(0);
            $table->float('common_amount',8,2)->after('ac_amount')->default(0);
            $table->float('water_amount',8,2)->after('common_amount')->default(0);
            $table->float('system_loss_amount',8,2)->after('water_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('electric_bills', function (Blueprint $table) {
            //
        });
    }
};
