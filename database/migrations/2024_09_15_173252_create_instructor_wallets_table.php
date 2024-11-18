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
        Schema::create('instructor_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')
                ->nullable()
                ->constrained('drivers')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->decimal('balance', 15, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_wallets');
    }
};
