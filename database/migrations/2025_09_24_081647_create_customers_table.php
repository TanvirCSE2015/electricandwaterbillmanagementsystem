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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('email')->unique()->nullable();
            $table->string('shop_no')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->unique();
            $table->string('phone')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('address')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
