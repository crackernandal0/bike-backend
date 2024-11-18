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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('profile_picture')->nullable();
            $table->text('address')->nullable();

            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->string('timezone')->nullable();
            $table->string('language')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('email_confirmed')->default(false);
            $table->boolean('mobile_confirmed')->default(false);

            $table->string('fcm_token')->nullable();
            $table->string('referral_code')->nullable();
        
            $table->foreignId('referred_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete()
            ->noActionOnUpdate();


            $table->string('social_provider')->nullable();
            $table->string('social_id')->nullable();

            $table->enum('login_device', ['android', 'ios'])->nullable();
            $table->string('last_known_ip')->nullable();
            $table->timestamp('last_active_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
