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
        Schema::create('driver_additional_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->cascadeOnDelete()->noActionOnUpdate();


            $table->text('qualifications')->nullable();
            $table->json('qualifications_attachments')->nullable();
            $table->json('certifications')->nullable();
            $table->string('training_specializations')->nullable();


            $table->text('additional_requests')->nullable();
            $table->text('service_preferences')->nullable();
            $table->string('available_from')->nullable();
            $table->enum('availability_schedule', ['Weekdays', 'Weekends', 'Flexible'])->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_additional_infos');
    }
};
