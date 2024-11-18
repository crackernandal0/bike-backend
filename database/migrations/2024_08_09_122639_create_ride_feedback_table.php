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
        Schema::create('ride_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')
                ->constrained('rides')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->tinyInteger('rating')->default(5); // Rating out of 5
            $table->text('feedback')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_feedback');
    }
};
