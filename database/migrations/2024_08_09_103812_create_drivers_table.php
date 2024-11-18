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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->nullable(); // Optional if registered with a phone number
            $table->string('country_code')->nullable();
            $table->string('phone_number')->unique();
            $table->string('language')->nullable(); // Driver's preferred language
            $table->date('date_of_birth');
            $table->text('address');
            $table->string('profile_photo')->nullable(); // Profile picture path
            $table->integer('experience_years')->default(0); // Experience in years
            $table->enum('role', ['driver', 'instructor', 'both'])->default('driver');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Account status (e.g., pending, approved, rejected)
            $table->boolean('account_status')->default(true); // Account status (e.g., pending, approved, rejected)

            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->string('timezone')->nullable();
            $table->boolean('active')->default(true);

            $table->foreignId('vehicle_type_id')->nullable()
                ->constrained('vehicle_types')
                ->nullOnDelete()->noActionOnUpdate();

            $table->foreignId('vehicle_subcategory_id')->nullable()
                ->constrained('vehicle_subcategories')
                ->nullOnDelete()->noActionOnUpdate();

            $table->foreignId('service_location_id')
                ->nullable()
                ->constrained('service_locations')
                ->cascadeOnDelete()->noActionOnUpdate();

            $table->boolean('available')->default(false); // Driver availability status
            $table->boolean('available_for_chauffeur')->default(false); // Chauffeur hire availability
            $table->boolean('available_for_trips')->default(false); // Trips Availability
            $table->integer('total_accepts')->default(0); // Total rides accepted
            $table->integer('total_rejects')->default(0); // Total rides rejected

            $table->integer('total_students')->default(0); // Total rides rejected
            $table->integer('total_ratings')->default(0); // Total rides rejected
            $table->text('instructor_bio')->nullable(); // Total rides rejected

            $table->string('joining_type')->default('Without Vehicle'); // With Vehicle or Without Vehicle
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->string('fcm_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
