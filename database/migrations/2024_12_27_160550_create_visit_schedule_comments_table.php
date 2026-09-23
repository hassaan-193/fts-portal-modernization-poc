<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitScheduleCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visit_schedule_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_schedule_id');
            $table->text('comment');
            $table->timestamps();
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
        Schema::dropIfExists('visit_schedule_comments');
    }
}
