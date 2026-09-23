<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabourAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labour_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('labor_id'); // Foreign key from staf_profile
            $table->unsignedInteger('project_id'); // Foreign key from projects
            $table->unsignedBigInteger('visit_schedule_id')->nullable(); // Nullable for normal projects
            $table->date('assignment_start_date')->nullable();
            $table->date('assignment_end_date')->nullable();
            $table->timestamps();
            $table->integer('hours_worked')->nullable(); // New column to store hours worked
            $table->foreign('labor_id')->references('id')->on('staf_profile')->onDelete('cascade');
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
        Schema::dropIfExists('labour_assignments');
    }
}
