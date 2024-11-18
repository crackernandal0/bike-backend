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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_location_id')->constrained('service_locations')->cascadeOnDelete()->noActionOnUpdate();
            $table->string('code');
            $table->integer('minimum_trip_amount')->default(0);
            $table->integer('maximum_discount_amount')->default(0);
            $table->integer('discount_percentage')->default(0);
            $table->integer('max_usage')->default(1);
            $table->integer('usage_count')->default(0);
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
