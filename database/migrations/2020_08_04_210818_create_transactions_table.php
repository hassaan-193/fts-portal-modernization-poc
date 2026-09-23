<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('date_time');
            $table->string('type');
            $table->integer('transactionable_id')->nullable();
            $table->string('transactionable_type')->nullable();
            $table->integer('expense_account')->unsigned()->nullable();
            $table->integer('account_id')->unsigned();
            $table->integer('transaction_type');
            $table->integer('payment_type');
            $table->string('payment_no')->nullable();
            $table->string('clearance_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->double('vat');
            $table->double('amount');
            $table->double('total');
            $table->text('note')->nullable();
            $table->integer('from_account')->nullable();
            $table->integer('status')->default(0);
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
        Schema::drop('transactions');
    }
}
