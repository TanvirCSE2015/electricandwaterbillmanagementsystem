<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('electric_bill_settings', function (Blueprint $table) {
            $table->id();
            $table->float('system_loss',8,2)->default(0);
            $table->float('demand_charge',8,2)->default(0);
            $table->float('service_charge',8,2)->default(0);
            $table->float('surcharge',8,2)->default(0);
            $table->float('vat',8,2)->default(0);
            $table->timestamps();

        });

        DB::table('electric_bill_settings')->insert([
            'system_loss' => 0,
            'demand_charge' => 0,
            'service_charge' => 0,
            'surcharge' => 0,
            'vat' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_bill_settings');
    }
};
