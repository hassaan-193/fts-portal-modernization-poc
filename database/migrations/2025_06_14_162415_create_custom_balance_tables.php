<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateCustomBalanceTables creates tables for balance accounting.
 *
 * @see \Illuminatech\Balance\BalanceDb
 */
class CreateCustomBalanceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('balance_transactions');
        Schema::dropIfExists('balance_accounts');

        Schema::create('balance_accounts', function (Blueprint $table) {
            $table->id('id');
            $table->double('balance')->default(0);
            $table->string('type')->index();
            $table->unsignedBigInteger('user_id')->nullable()->default(1);
            $table->string('code')->nullable();
            $table->string('account_type')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });

        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('extra_account_id')->nullable();
            $table->double('amount')->default(0);
            $table->json('data')->nullable();
            $table->timestamp('created_at');

            $table->foreign('account_id')
                ->references('id')
                ->on('balance_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('extra_account_id')
                ->references('id')
                ->on('balance_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_transactions');
        Schema::dropIfExists('balance_accounts');
    }
}
