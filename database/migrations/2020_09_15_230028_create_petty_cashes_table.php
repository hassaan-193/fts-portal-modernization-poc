<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePettyCashesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petty_cashes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('voucher_no')->nullable();
            $table->integer('account_id')->unsigned();
            $table->integer('vendor_id')->nullable()->unsigned();
            $table->bigInteger('user_id')->nullable();
            $table->integer('project_id')->nullable()->unsigned();
            $table->timestamp('date_time');
            $table->integer('type');
            $table->text('description')->nullable();
            $table->float('amount');
            $table->float('vat');
            $table->float('total_amount');
            $table->integer('is_advance')->nullable();
            $table->integer('is_user_deduction')->nullable();
            $table->timestamps();
            $table->foreign('vendor_id')->references('id')->on('vendors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('petty_cashes');
    }
}
