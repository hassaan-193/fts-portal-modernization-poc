<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyStaffReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_staff_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('staff_id');
            $table->foreign('staff_id')->references('id')->on('staf_profile')->onDelete('cascade');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');

            $table->float('safety_avg');
            $table->float('communication_avg');
            $table->float('attendance_avg');
            $table->float('time_management_avg');
            $table->float('job_responsibility_avg');
            $table->float('material_handling_avg');
            $table->float('document_handling_avg');
            $table->float('competency_avg');

            $table->float('final_score');
            $table->timestamps();

            $table->unique(['staff_id', 'month', 'year'], 'unique_monthly_report');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_staff_reports');
    }
}
