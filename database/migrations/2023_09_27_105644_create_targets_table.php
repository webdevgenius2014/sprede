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
        Schema::create('targets', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('target_unique_id')->unique()->nullable();
            $table->string('title')->nullable();
            $table->boolean('type')->default('0'); // 0 => public, 1 => private 
            $table->integer('interest_id')->unsigned()->nullable();
            $table->integer('sub_interest_id')->unsigned()->nullable();
            $table->string('target_units')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->longText('description')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('incentive')->default('1'); // 0 => unincentivized, 1 => incentivized 
            $table->string('incentive_prize')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); //On User delete this data will be also deleted
            $table->foreign('interest_id')->references('id')->on('interests')->onDelete('cascade'); //On Insterest delete this data will be also deleted
            $table->foreign('sub_interest_id')->references('id')->on('sub_interests')->onDelete('cascade'); //On Sub Insterest delete this data will be also deleted

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
