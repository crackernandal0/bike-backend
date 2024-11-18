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
        Schema::create('ride_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->noActionOnUpdate();


            $table->foreignId('ride_id')
                ->constrained('rides')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->string('merchant_transaction_id')->unique();
            $table->string('provider_reference_id')->nullable();
            $table->decimal('payment_amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_payments');
    }
};