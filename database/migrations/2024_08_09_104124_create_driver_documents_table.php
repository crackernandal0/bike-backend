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
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->cascadeOnDelete()->noActionOnUpdate();

            $table->string('document_type'); // e.g., Driving License, Aadhar, PAN, etc.
            $table->string('document_number')->nullable(); // e.g., License Number, Aadhar Number, etc.
            $table->string('document_photo')->nullable(); // Path to the uploaded document photo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_documents');
    }
};
