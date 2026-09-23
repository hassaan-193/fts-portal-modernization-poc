<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceBanksTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('beneficary_account_name');
            $table->string('bank_name');
            $table->string('bank_branch')->nullable();;
            $table->string('account_no');
            $table->string('account_currency');
            $table->string('iban_no')->nullable();;
            $table->string('swift_code')->nullable();;
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
        Schema::drop('invoice_banks');
    }
}
