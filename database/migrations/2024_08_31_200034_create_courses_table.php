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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('drivers')->cascadeOnDelete(); // Assuming an instructors table exists
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('admin_commision')->default(0);
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // For storing course features as JSON
            $table->string('curriculum_title')->nullable();
            $table->text('curriculum')->nullable(); // For storing curriculum details as JSON
            $table->integer('total_enrollments')->default(0);
            $table->string('banner_image')->nullable(); // For storing the banner image path
            $table->string('duration')->nullable(); // For storing the banner image path
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
