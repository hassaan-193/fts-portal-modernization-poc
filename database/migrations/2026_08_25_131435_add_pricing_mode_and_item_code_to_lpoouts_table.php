<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingModeAndItemCodeToLpooutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lpoouts', function (Blueprint $table) {
            // Carried over from the purchase order so the LPO print / PDF / vendor
            // email can drop the price columns on a lump-sum order.
            $table->string('pricing_mode')->default('unit')->after('items');
            $table->boolean('has_item_code')->default(false)->after('pricing_mode');
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
            $table->dropColumn(['pricing_mode', 'has_item_code']);
        });
    }
}
