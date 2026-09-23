<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('staff_ratings', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('staff_id');
        $table->foreign('staff_id')->references('id')->on('staf_profile')->onDelete('cascade');
        $table->foreignId('engineer_id')->constrained('users')->onDelete('cascade');
        $table->unsignedTinyInteger('month');
        $table->unsignedSmallInteger('year');
        
        $table->unsignedTinyInteger('safety_compliance');
        $table->unsignedTinyInteger('communication');
        $table->unsignedTinyInteger('attendance');
        $table->unsignedTinyInteger('time_management');
        $table->unsignedTinyInteger('job_responsibility');
        $table->unsignedTinyInteger('material_handling');
        $table->unsignedTinyInteger('document_handling');
        $table->unsignedTinyInteger('competency');

        $table->timestamps();

        $table->unique(['staff_id', 'engineer_id', 'month', 'year'], 'unique_rating_per_engineer');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_ratings');
    }
}
