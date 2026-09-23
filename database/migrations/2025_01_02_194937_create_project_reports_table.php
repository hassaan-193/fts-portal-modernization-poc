<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projectreports', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('reference_number');
            $table->date('date');
            $table->time('inspector_visiting_time')->nullable();
            $table->time('inspector_leaving_time')->nullable();
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('project_id')->nullable();
            $table->unsignedBigInteger('visit_schedule_id')->nullable();
            $table->string('site_location')->nullable();
            $table->string('site_name');    
            $table->text('used_items')->nullable();
            $table->text('required_items')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'approved', 'disapproved'])->default('pending');
            $table->timestamps();
            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
        Schema::dropIfExists('project_reports');
    }
}
