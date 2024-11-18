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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->noActionOnUpdate();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete()->noActionOnUpdate();
            $table->text('address');
            $table->string('phone')->nullable();
            $table->enum('payment_method', ['gateway', 'wallet', 'cash']);
            $table->decimal('total_price', 10, 2);
            $table->enum('order_status', ['pending', 'accepted', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->enum('delivery_status', ['pending', 'delivered', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
