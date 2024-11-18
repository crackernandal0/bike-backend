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
        Schema::create('instructor_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_wallet_id')
                ->constrained('instructor_wallets')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->uuid('transaction_id')->nullable();

            $table->enum('type', ['credit', 'debit', 'withdraw']);
            $table->decimal('amount', 15, 2);
            $table->string('reference')->nullable(); // Can be used for reference to orders or other entities
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_wallet_transactions');
    }
};
