<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitScheduleHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
// Migration for visit_schedule_history table
Schema::create('visit_schedule_history', function (Blueprint $table) {
    $table->id(); 
    $table->unsignedInteger('project_id'); 
    $table->unsignedBigInteger('visit_schedule_id')->nullable(); 
    $table->date('visit_date')->nullable();
    $table->string('status');
    $table->string('company_name');
    $table->string('project_name');
    $table->timestamps();
    $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
    $table->foreign('visit_schedule_id')->references('id')->on('visit_schedules')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visit_schedule_history');
    }
}
