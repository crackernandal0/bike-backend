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
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('trip_categories')->nullOnDelete()->noActionOnUpdate();
            $table->string('package_name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->json('detailed_itinerary')->nullable();
            $table->json('tour_stops_places')->nullable();
            $table->double('tour_price');
            $table->double('admin_commission');
            $table->string('banner_image');
            $table->string('duration');
            $table->string('members')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};
