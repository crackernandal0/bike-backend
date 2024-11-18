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
        Schema::create('driver_ride_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')
                ->constrained('rides')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->enum('request_status', ['pending', 'accepted', 'declined', 'canceled'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_ride_requests');
    }
};
