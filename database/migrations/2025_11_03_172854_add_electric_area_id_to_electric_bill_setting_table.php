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
        Schema::table('electric_bill_settings', function (Blueprint $table) {
            $table->foreignId('electric_area_id')->nullable()->constrained()->onDelete('set null')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('electric_bill_settings', function (Blueprint $table) {
            //
        });
    }
};
