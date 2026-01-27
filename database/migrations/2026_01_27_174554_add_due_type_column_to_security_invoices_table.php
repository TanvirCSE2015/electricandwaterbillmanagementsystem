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
        Schema::table('security_invoices', function (Blueprint $table) {
            $table->enum('due_type', ['pre_due', 'current'])->after('s_note')->default('current');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('security_invoices', function (Blueprint $table) {
            //
        });
    }
};
