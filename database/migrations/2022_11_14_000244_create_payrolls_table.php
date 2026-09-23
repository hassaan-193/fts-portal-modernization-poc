<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->nullable();
            $table->integer('member_id')->unsigned();
            $table->integer('absents')->nullable();
            $table->double('hours')->nullable();
            $table->double('plus_adjustment')->default(0);
            $table->double('minus_adjustment')->default(0);
            $table->double('total_amount');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->foreign('member_id')->references('id')->on('staf_profile')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payrolls');
    }
}
