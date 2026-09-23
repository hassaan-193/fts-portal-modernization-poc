<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoicesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no');
            $table->integer('invoice_type_id')->unsigned();
            $table->integer('quotation_id')->unsigned();
            $table->integer('invoice_request_id')->unsigned();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('currency')->default('AED');
            $table->string('payment_terms')->nullable();
            $table->string('amount_in_word')->nullable();
            $table->text('note1')->nullable();
            $table->text('note2')->nullable();
            $table->integer('invoice_bank_id')->unsigned()->default(0);
            $table->double('amount')->default(0);
            $table->double('vat')->default(0);
            $table->double('total_amount')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
            $table->foreign('invoice_type_id')->references('id')->on('invoice_types')->onDelete('cascade');;
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('invoices');
    }
}
