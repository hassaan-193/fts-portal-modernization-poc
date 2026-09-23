<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visit_schedules', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedInteger('project_id'); 
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->date('visit_date'); 
            $table->enum('status', ['pending', 'done', 'upcoming'])->default('pending'); 
            $table->boolean('file_uploaded')->default(false); 
            $table->string('file_path')->nullable(); 
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
        Schema::dropIfExists('visit_schedules');
    }
}
