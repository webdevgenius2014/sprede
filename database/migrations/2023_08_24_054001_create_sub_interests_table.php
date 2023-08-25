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
        Schema::create('sub_interests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('interest_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->boolean('default_sub_cat')->default('1');
            $table->string('name');
            $table->timestamps();
            $table->foreign('interest_id')->references('id')->on('interests')->onDelete('cascade'); //On Insterest delete this data will be also deleted
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); //On User delete this data will be also deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_interests');
    }
};
