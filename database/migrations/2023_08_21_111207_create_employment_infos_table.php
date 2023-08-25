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
        Schema::create('employment_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->boolean('is_defence')->default(0);
            $table->boolean('on_privacy')->default(0);
            $table->string('organization')->nullable();
            $table->string('designation')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->boolean('current_work_here')->default('0');
            $table->string('org_city')->nullable();
            $table->string('org_country')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); //On user delete this data will be also deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employement_infos');
    }
};
