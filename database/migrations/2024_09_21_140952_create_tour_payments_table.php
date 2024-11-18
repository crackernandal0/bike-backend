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
        Schema::create('tour_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_tour_booking_id')->constrained('user_tour_bookings')->cascadeOnDelete()->noActionOnUpdate();

            $table->unsignedBigInteger('user_id');
            $table->string('transaction_id')->nullable();
            $table->enum('payment_type', ['wallet', 'gateway']);
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->string('provider_reference_id')->nullable(); // For gateway reference
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_payments');
    }
};
