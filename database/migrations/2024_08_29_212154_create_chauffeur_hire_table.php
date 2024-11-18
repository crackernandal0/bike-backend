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
        Schema::create('chauffeur_hire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('chauffeur_id')->constrained('users')->onDelete('cascade');
            $table->string('pickup');
            $table->string('dropoff');
            $table->string('pickup_location_type')->nullable();
            $table->string('destination_location_type')->nullable();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('vehicle_type');
            $table->string('preferred_vehicle')->nullable();
            $table->enum('chauffeur_type', ['with_vehicle', 'without_vehicle']);
            $table->string('hire_type');
            $table->string('event_type')->nullable();
            $table->unsignedTinyInteger('child_seats')->default(0);
            $table->text('specific_vehicle_models')->nullable();
            $table->text('additional_amenities')->nullable();
            $table->text('additional_requests')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('admin_commission', 10, 2)->default(0);
            $table->decimal('gst', 5, 2)->default(0);
            $table->decimal('service_tax', 5, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'canceled', 'rejected', 'service_stopped', 'completed'])->default('pending');
            $table->enum('payment_status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chauffeur_hire');
    }
};
