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
        Schema::create('chauffeur_hire_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chauffeur_hire_id');
            $table->unsignedBigInteger('user_id');
            $table->string('transaction_id')->nullable();
            $table->enum('payment_type', ['wallet', 'gateway']);
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->string('provider_reference_id')->nullable(); // For gateway reference
            $table->foreign('chauffeur_hire_id')->references('id')->on('chauffeur_hire')->cascadeOnDelete()->noActionOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chauffeur_hire_payments');
    }
};
