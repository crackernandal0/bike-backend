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
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reference to the user
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade'); // Reference to the lesson
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started'); // Status of the lesson

            $table->date('learning_day')->nullable();
            $table->time('learning_time')->nullable();
            $table->timestamp('started_at')->nullable(); // When the lesson was started
            $table->timestamp('completed_at')->nullable(); // When the lesson was completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
