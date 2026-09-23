<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceRequestProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_request_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_id')->unsigned();
            $table->string('product');
            $table->string('unit')->nullable();
            $table->integer('qty');
            $table->double('rate');
            $table->double('amount');
            $table->double('vat');
            $table->double('total_amount');
            $table->timestamps();
            $table->foreign('request_id')->references('id')->on('invoice_requests')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_request_products');
    }
}
