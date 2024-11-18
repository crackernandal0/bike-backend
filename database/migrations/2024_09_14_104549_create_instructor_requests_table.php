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
        Schema::create('instructor_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete()->noActionOnUpdate();
            $table->text('qualifications')->nullable();
            $table->json('qualifications_attachments')->nullable();
            $table->json('certifications')->nullable();
            $table->string('training_specializations')->nullable();
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
        Schema::dropIfExists('instructor_requests');
    }
};
