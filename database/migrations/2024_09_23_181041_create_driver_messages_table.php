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
        Schema::create('driver_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_chat_session_id')->constrained()->cascadeOnDelete()->noActionOnUpdate();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->cascadeOnDelete()->noActionOnUpdate();
            $table->foreignId('instructor_id')->nullable()->constrained('drivers')->cascadeOnDelete()->noActionOnUpdate();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->cascadeOnDelete()->noActionOnUpdate();
            $table->text('message')->nullable();
            $table->string('image')->nullable(); // Optional image
            $table->string('sender_type')->default('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_messages');
    }
};
