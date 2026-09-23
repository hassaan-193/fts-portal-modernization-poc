<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLpoutFieldsToPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('lpout_name')->nullable()->after('lpout_id');
            $table->bigInteger('lpout_vendor_id')->nullable()->after('lpout_name');
            $table->string('lpout_trn_no')->nullable()->after('lpout_vendor_id');
            $table->string('lpout_kindly_attn')->nullable()->after('lpout_trn_no');
            $table->date('lpout_date')->nullable()->after('lpout_kindly_attn');
            $table->string('lpout_payment_type')->nullable()->after('lpout_date');
            $table->date('lpout_cheque_date')->nullable()->after('lpout_payment_type');
            $table->tinyInteger('lpout_vat')->nullable()->default(0)->after('lpout_cheque_date');
            $table->string('lpout_payment_preference_option')->nullable()->default('default')->after('lpout_vat');
            $table->string('lpout_payment_preference')->nullable()->after('lpout_payment_preference_option');
            $table->integer('lpout_pdc_number_of_days')->nullable()->after('lpout_payment_preference');
            $table->string('lpout_pdc_payment_option')->nullable()->after('lpout_pdc_number_of_days');
            $table->json('lpout_items')->nullable()->after('lpout_pdc_payment_option');
            $table->longText('lpout_terms')->nullable()->after('lpout_items');
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'lpout_name',
                'lpout_vendor_id',
                'lpout_trn_no',
                'lpout_kindly_attn',
                'lpout_date',
                'lpout_payment_type',
                'lpout_cheque_date',
                'lpout_vat',
                'lpout_payment_preference_option',
                'lpout_payment_preference',
                'lpout_pdc_number_of_days',
                'lpout_pdc_payment_option',
                'lpout_items',
                'lpout_terms'
            ]);
        });
    }
}
