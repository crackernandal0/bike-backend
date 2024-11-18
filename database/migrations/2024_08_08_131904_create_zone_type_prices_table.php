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
        Schema::create('zone_type_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')
                ->constrained('zones')
                ->cascadeOnDelete()
                ->noActionOnUpdate();
                
                $table->foreignId('vehicle_type_id')
                ->constrained('vehicle_types')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

                $table->foreignId('vehicle_subcategory_id')
                ->nullable()
                ->constrained('vehicle_subcategories')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->string('payment_type')->comment("Cash,Wallet,Gateway");

            $table->double('base_price', 10, 2)->default(0);
            $table->integer('base_distance');
            $table->double('price_per_distance', 10, 2)->default(0);
            $table->double('waiting_charge', 10, 2)->default(0);
            $table->double('price_per_time', 10, 2)->default(0);
            $table->double('cancellation_fee', 10, 2)->default(0);

            $table->double('admin_commision');
            $table->double('service_tax');
            $table->double('gst_tax');
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_types');
    }
};
