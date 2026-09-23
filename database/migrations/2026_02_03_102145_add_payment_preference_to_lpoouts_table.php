<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentPreferenceToLpooutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lpoouts', function (Blueprint $table) {
            $table->string('lpo_payment_preference')->nullable()->after('status');
            $table->integer('lpo_pdc_number_of_days')->nullable()->after('lpo_payment_preference');
            $table->string('lpo_pdc_payment_option')->nullable()->after('lpo_pdc_number_of_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lpoouts', function (Blueprint $table) {
            $table->dropColumn(['lpo_payment_preference', 'lpo_pdc_number_of_days', 'lpo_pdc_payment_option']);
        });
    }
}
