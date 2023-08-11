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
            $table->string('email')->unique()->nullable();
            $table->string('email_otp')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('name');
            $table->string('username')->unique()->nullable();
            $table->boolean('update_username')->default(0);
            $table->string('mobile')->unique()->nullable();   
            $table->string('mobile_verifed_at')->nullable();
            $table->string('password');
            $table->string('profile_url')->nullable();
            $table->string('email_otp_expires_in')->nullable(); 
            $table->enum('terms', ['0', '1'])->default('0')->nullable();
            $table->enum('news_updates', ['0', '1'])->default('0')->nullable(); ;      
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
