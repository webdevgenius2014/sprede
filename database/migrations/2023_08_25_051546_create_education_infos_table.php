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
        Schema::create('education_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('education');
            $table->string('university');
            $table->string('from');
            $table->string('to');
            $table->boolean('current_pursuing')->default('0');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); //On User delete this data will be also deleted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_infos');
    }
};
