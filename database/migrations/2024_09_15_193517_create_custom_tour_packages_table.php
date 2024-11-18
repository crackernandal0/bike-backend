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
        Schema::create('custom_tour_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->noActionOnUpdate();
            $table->string('pickup_location');
            $table->string('tour_location');
            $table->foreignId('vehicle_subcategory_id')->nullable()->constrained('vehicle_subcategories')->nullOnDelete()->noActionOnUpdate();
            $table->date('start_date');
            $table->date('return_date');
            $table->integer('no_of_passengers');
            $table->double('budget');
            $table->text('special_requests')->nullable();
            $table->json('stops')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_tour_packages');
    }
};
