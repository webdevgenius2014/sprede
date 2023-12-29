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
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('organization_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->boolean('type')->default('0'); // 0 => public, 1 => private
            $table->string('location')->nullable();
            $table->string('event_date')->nullable();
            $table->string('start_time')->nullable();
            $table->string('meridiem')->nullable(); // AM or PM
            $table->longText('description')->nullable();
            $table->string('cover_photo')->nullable();
            $table->integer('target_id')->unsigned()->nullable();
            $table->string('validator_charging_mode')->nullable();
            $table->integer('validator_id')->unsigned()->nullable();
            $table->integer('validator_optional_id')->unsigned()->nullable(); // optional
            $table->integer('vendor_id')->unsigned()->nullable();
            // $table->boolean('complexity')->default('0'); // 0 => Simple, 1 => Complex
            $table->boolean('frequency')->default('0'); // 0 => One Time, 1 => Recurring, 2 => Ad-Hoc
            $table->string('frequency_start_date')->nullable();
            $table->string('frequency_end_date')->nullable();
            $table->string('recurring_time')->nullable(); // 1, 2, 3
            $table->string('continuous_time')->nullable(); //daily, weekly, monthly, quarterly, half-yearly, yearly
            $table->string('mode_of_participation')->nullable(); //
            $table->string('payment')->default('0'); // 0 => Free
            $table->boolean('incentive_type')->default('0'); // 0 => Incentivized, 1 => Unincentivized
            $table->string('incentive_price')->nullable(); // Certificate, Medals, Vouchers, Cash, Others
            $table->longText('event_activity')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); //On User delete this data will be also deleted
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('target_id')->references('id')->on('targets')->onDelete('cascade');
            $table->foreign('validator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('validator_optional_id')->references('id')->on('users')->onDelete('cascade'); //On User delete this data will be also deleted
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade'); //On User delete this data will be also deleted
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};