<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentInvoicesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->string('invoice_no')->nullable();
            $table->integer('lpoout_id')->nullable()->unsigned();
            $table->integer('invoice_request_id')->nullable();
            $table->integer('vendor_id')->nullable();
            $table->integer('project_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->double('amount');
            $table->double('vat');
            $table->double('total_amount');
            $table->text('note')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
            $table->foreign('lpoout_id')->nullable()->references('id')->on('lpoouts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payment_invoices');
    }
}
