<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLpooutsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lpoouts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lpo_invoice_no')->nullable();
            $table->string('name');
            $table->integer('lpo_out_type_id')->unsigned();
            $table->integer('project_id')->unsigned()->nullable();
            $table->integer('vendor_id')->unsigned();
            $table->date('date')->nullable();
            $table->double('amount');
            $table->double('vat');
            $table->double('total_amount');
            $table->text('file');
            $table->timestamps();
            $table->foreign('lpo_out_type_id')->references('id')->on('lpo_out_types')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lpoouts');
    }
}
