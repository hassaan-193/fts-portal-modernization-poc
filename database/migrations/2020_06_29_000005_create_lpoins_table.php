<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLpoinsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lpoins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quotation_id')->unsigned();
            $table->string('ref_no')->nullable();
            $table->string('payment_terms')->nullable();
            $table->double('civil_defence_fee')->default(0);
            $table->double('government_fee')->default(0);
            $table->double('adjustment_fee')->default(0);
            $table->date('date_issue')->nullable();
            $table->date('date_due')->nullable();
            $table->float('amount')->nullable();
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
        Schema::drop('lpoins');
    }
}
