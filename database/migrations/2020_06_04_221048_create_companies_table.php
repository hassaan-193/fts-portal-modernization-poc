<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('contact_no_two')->nullable();
            $table->string('vat_no')->nullable();
            $table->text('location')->nullable();
            $table->text('file')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('billing_contact_person')->nullable();
            $table->string('billing_pob')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('shipping_contact_person')->nullable();
            $table->string('shipping_pob')->nullable()->nullable();
            $table->string('shipping_email')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('credit_limit')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
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
        Schema::drop('companies');
    }
}
