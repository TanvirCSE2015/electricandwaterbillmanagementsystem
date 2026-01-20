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
        Schema::table('water_settings', function (Blueprint $table) {
            $table->float('monthly_security',8,2)->after('monthly_rate')->default(0);
            $table->float('const_security',8,2)->after('monthly_const_rate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_settings', function (Blueprint $table) {
            $table->dropColumn(['monthly_security', 'const_security']);
        });
    }
};
