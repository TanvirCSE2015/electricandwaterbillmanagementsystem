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
        Schema::create('water_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('customer_phone')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('holding_number')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('flat_number')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->integer('total_flat')->default(0);
            $table->float('previous_due')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_customers');
    }
};
