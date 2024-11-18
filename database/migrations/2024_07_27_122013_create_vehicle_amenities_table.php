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
        Schema::create('vehicle_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_subcategory_id')
                ->constrained('vehicle_subcategories')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('amenity_id')
                ->constrained('amenities')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_amenities');
    }
};
