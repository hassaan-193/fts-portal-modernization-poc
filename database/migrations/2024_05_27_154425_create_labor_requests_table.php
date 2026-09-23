<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaborRequestsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labor_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('labor_id');
            $table->string('type')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->double('advance_money')->nullable();
            $table->text('note')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();

            $table->foreign('labor_id')->references('id')->on('staf_profile')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('labor_requests');
    }
}
