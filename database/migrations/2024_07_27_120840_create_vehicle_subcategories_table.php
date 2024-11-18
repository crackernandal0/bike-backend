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
        Schema::create('vehicle_subcategories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // E.g., Sedan, SUV, etc.
            $table->foreignId('vehicle_type_id')
            ->constrained('vehicle_types')
            ->cascadeOnDelete()
            ->noActionOnUpdate();
            $table->integer('passangers')->default(1);
            $table->string('image')->nullable();
            $table->text('short_amenties')->nullable(); // E.g., Sedan, SUV, etc.
            $table->json('specifications')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_categories');
    }
};
