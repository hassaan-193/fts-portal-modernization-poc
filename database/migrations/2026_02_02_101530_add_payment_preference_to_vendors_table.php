<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentPreferenceToVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('payment_preference')->default('cod')->after('file');
            $table->integer('pdc_number_of_days')->nullable()->after('payment_preference');
            $table->string('pdc_payment_option')->nullable()->after('pdc_number_of_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['payment_preference', 'pdc_number_of_days', 'pdc_payment_option']);
        });
    }
}
