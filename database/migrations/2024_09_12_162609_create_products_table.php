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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->cascadeOnDelete()->noActionOnUpdate();
            $table->string('title');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->integer('available_quantity')->nullable();
            $table->integer('used_quantity')->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0.00);
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete()->noActionOnUpdate();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
