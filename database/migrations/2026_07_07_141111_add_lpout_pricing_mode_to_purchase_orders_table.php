<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLpoutPricingModeToPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // 'unit' = per-unit pricing (qty x unit_price), 'lump' = single batch total
            $table->string('lpout_pricing_mode')->default('unit')->after('lpout_items');
            // Batch total typed by the user when pricing mode is 'lump'
            $table->decimal('lpout_manual_total', 15, 2)->nullable()->after('lpout_pricing_mode');
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
            $table->dropColumn(['lpout_pricing_mode', 'lpout_manual_total']);
        });
    }
}
