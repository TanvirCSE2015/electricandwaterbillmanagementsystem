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
        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('meter_number')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->unique();
            $table->enum('status', ['active', 'destroyed','replaced','inactive'])->default('active');
            $table->date('install_at')->nullable();
            $table->date('uninstall_at')->nullable();
            $table->text('remarks')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meters');
    }
};
