<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemCodeFlagToPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // When on, every line item carries an `item_code` and the ITEM CODE
            // column is rendered before the material name on every screen/print.
            // Independent of pricing mode - it applies to per-unit and lump-sum alike.
            $table->boolean('has_item_code')->default(false)->after('lpout_manual_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('has_item_code');
        });
    }
}
