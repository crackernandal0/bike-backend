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
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete()
                ->noActionOnUpdate();
            $table->foreignId('vehicle_subcategory_id')
                ->nullable()
                ->constrained('vehicle_subcategories')
                ->nullOnDelete()
                ->noActionOnUpdate();
        
            // Fields from the vehicles table
            $table->foreignId('type_id')
                ->constrained('vehicle_types')
                ->cascadeOnDelete()
                ->noActionOnUpdate();
        
            $table->string('color')->nullable();
            $table->string('license_plate')->unique();
            $table->year('year');
            $table->string('image')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('vehicle_passing_till')->nullable();
            $table->json('special_services')->nullable();
            $table->boolean('status')->default(1);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_models');
    }
};
