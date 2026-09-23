<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevisedFromLpooutIdToPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedInteger('revised_from_lpoout_id')->nullable()->after('lpout_id');
            $table->foreign('revised_from_lpoout_id')
                ->references('id')
                ->on('lpoouts')
                ->onDelete('set null');
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
            $table->dropForeign(['revised_from_lpoout_id']);
            $table->dropColumn('revised_from_lpoout_id');
        });
    }
}
