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
        Schema::create('user_tour_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->nullable()->constrained('tour_packages')->nullOnDelete()->noActionOnUpdate();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->noActionOnUpdate();
            $table->text('pickup_location');
            $table->foreignId('vehicle_subcategory_id')->nullable()->constrained('vehicle_subcategories')->nullOnDelete()->noActionOnUpdate();
            $table->date('booking_date');
            $table->integer('no_of_passengers');
            $table->string('promo_code')->nullable();
            $table->text('special_requests')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->enum('booking_status', ['confirmed', 'pending', 'cancelled', 'completed'])->default('pending');
            $table->json('stops')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tour_bookings');
    }
};
