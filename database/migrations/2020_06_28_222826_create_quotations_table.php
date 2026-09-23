<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuotationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('company_id')->unsigned();
            $table->string('ref_no')->nullable();
            $table->integer('quotation_type_id')->nullable();
            $table->integer('quotation_company')->nullable();
            $table->double('amount');
            $table->double('vat');
            $table->double('total_amount');
            $table->date('date')->nullable();
            $table->string('subject')->nullable();
            $table->text('location')->nullable();
            $table->text('file')->nullable();
            $table->text('payment')->nullable();
            $table->text('exclusion')->nullable();
            $table->integer('status')->unsigned()->default(0);
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('quotations');
    }
}
