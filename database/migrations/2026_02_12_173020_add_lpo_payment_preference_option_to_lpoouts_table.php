<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLpoPaymentPreferenceOptionToLpooutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lpoouts', function (Blueprint $table) {
            if (!Schema::hasColumn('lpoouts', 'lpo_payment_preference_option')) {
                $table->string('lpo_payment_preference_option')->default('default')->after('status');
            }
            if (!Schema::hasColumn('lpoouts', 'terms')) {
                $table->text('terms')->nullable()->after('lpo_pdc_payment_option');
            }
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
            if (Schema::hasColumn('lpoouts', 'lpo_payment_preference_option')) {
                $table->dropColumn('lpo_payment_preference_option');
            }
            if (Schema::hasColumn('lpoouts', 'terms')) {
                $table->dropColumn('terms');
            }
        });
    }
}
