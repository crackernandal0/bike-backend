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
        Schema::create('chauffeur_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chauffeur_id')
                ->constrained('chauffeurs')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->noActionOnUpdate();
            $table->integer('rating')->default(0); // Rating value (e.g., 1 to 5 stars)
            $table->text('review')->nullable(); // Optional review text
         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chauffeur_ratings');
    }
};
