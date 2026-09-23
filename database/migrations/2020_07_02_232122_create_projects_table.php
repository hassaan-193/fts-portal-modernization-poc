<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->nullable();
            $table->integer('quotation_id')->unsigned();
            $table->integer('project_type_id')->unsigned();
            $table->string('subject')->nullable();
            $table->string('payment_terms')->nullable();
            $table->double('labour_charges')->nullable();
            $table->double('material_charges')->nullable();
            $table->string('project_source')->nullable();
            $table->string('project_estimation')->nullable();
            $table->integer('user_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('projects');
    }
}
