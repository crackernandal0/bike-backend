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
        Schema::create('driver_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->cascadeOnDelete()->noActionOnUpdate();
            $table->foreignId('instructor_id')->nullable()->constrained('drivers')->cascadeOnDelete()->noActionOnUpdate();
            $table->foreignId('admin_id')->nullable()->constrained()->cascadeOnDelete()->noActionOnUpdate();
            $table->boolean('is_active')->default(true); // Only one active session per user
            $table->string('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_chat_sessions');
    }
};
