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
        Schema::create('driver_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete()->noActionOnUpdate();
            $table->boolean('available_for_chauffeur')->default(false); // Chauffeur hire availability
            $table->boolean('available_for_trips')->default(false); // Trips availability
            $table->string('joining_type')->default('Without Vehicle'); // With Vehicle or Without Vehicle
            $table->text('additional_requests')->nullable();
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_requests');
    }
};
