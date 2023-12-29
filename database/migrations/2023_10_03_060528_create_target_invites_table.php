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
        Schema::create('target_invites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('target_id')->unsigned()->nullable();
            $table->integer('invited_user_id')->unsigned()->nullable();
            $table->boolean('accepted')->default('0')->nullable();
            $table->timestamps();

            $table->foreign('target_id')->references('id')->on('targets')->onDelete('cascade'); //On TAERGET delete this data will be also deleted
            $table->foreign('invited_user_id')->references('id')->on('users')->onDelete('cascade'); //On User delete this data will be also deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_invites');
    }
};
