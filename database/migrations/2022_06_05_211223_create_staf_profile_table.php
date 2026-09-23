<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStafProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staf_profile', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('staf_type')->nullable();
            $table->string('last_name')->nullable();
            $table->string('nationality')->nullable();
            $table->string('gender')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('dob')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->date('visa_expiry')->nullable();
            $table->date('emirates_id_expiry')->nullable();
            $table->date('labor_card_expiry')->nullable();
            $table->date('driver_permit_expiry')->nullable();
            $table->date('last_vacation_start')->nullable();
            $table->date('last_vacation_end')->nullable();
            $table->integer('last_vacation_days')->nullable();
            $table->date('last_increment')->nullable();
            $table->integer('last_increment_amount')->nullable();
            $table->double('basic_salary')->default(0);
            $table->double('total_salary')->default(0);
            $table->double('overtime_rate')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staf_profile');
    }
}
