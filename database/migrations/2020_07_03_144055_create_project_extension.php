<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectExtension extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_extension', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->double('value');
            $table->integer('project_id')->unsigned();
            $table->integer('quotation_id')->unsigned();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('quotation_id')->references('id')->on('quotations');
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
        Schema::dropIfExists('project_extension');
    }
}
