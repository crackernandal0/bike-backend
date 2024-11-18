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
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->string('ride_number')->unique();

            $table->enum('ride_status', [
                'pending',
                'accepted',
                'driver_arrived',
                'in_progress',
                'waiting',
                'completed',
                'canceled',
                'no_show'
            ])->default('pending');

            $table->string('ride_otp')->nullable();
            $table->boolean('instant_ride')->default(false);
            $table->boolean('ride_later')->default(false);

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('service_location_id')
                ->nullable()
                ->constrained('service_locations')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('vehicle_type_id')
                ->nullable()
                ->constrained('vehicle_types')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('vehicle_subcategory_id')
                ->nullable()
                ->constrained('vehicle_subcategories')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('zone_id')
                ->nullable()
                ->constrained('zones')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('zone_type_id')
                ->nullable()
                ->constrained('zone_type_prices')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('drivers')
                ->nullOnDelete()
                ->noActionOnUpdate();


            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();


            $table->tinyInteger('is_schedule_ride')->default(0);
            $table->enum('ride_type', ['simple', 'outstation', 'rental'])->default('simple');
            $table->boolean('return_trip')->default(false);
            $table->date('return_date')->nullable();
            $table->time('return_time')->nullable();
            $table->unsignedTinyInteger('passenger_count')->default(1);

            $table->boolean('is_for_someone_else')->default(false);
            $table->string('rider_name')->nullable();
            $table->string('rider_phone_number')->nullable();
            $table->text('additional_notes')->nullable(); // Notes for the driver

            $table->timestamp('ride_accepted_at')->nullable();
            $table->timestamp('driver_arrived_at')->nullable();
            $table->timestamp('ride_started_at')->nullable();
            $table->timestamp('ride_completed_at')->nullable();

            $table->timestamp('ride_cancelled_at')->nullable();
            $table->string('cancel_type')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->string('canceled_by')->nullable();
            $table->decimal('cancellation_fee', 10, 2)->nullable();


            $table->enum('payment_type', ['Cash', 'Wallet', 'Gateway']);
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->decimal('final_fare', 10, 2)->nullable();
            $table->decimal('user_coins_discount', 10, 2)->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');


            $table->timestamp('ride_booked_at')->nullable();

            $table->string('total_distance')->nullable();
            $table->string('estimated_time')->nullable();



            $table->integer('waiting_minutes')->nullable();
            $table->decimal('waiting_charges', 10, 2)->nullable();


            $table->foreignId('promo_id')
                ->nullable()
                ->constrained('promos')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->json('pickup')->nullable();  // long, lat, address
            $table->json('dropoff')->nullable(); // long, lat, address

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
