<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLpoutIdToPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedInteger('lpout_id')->nullable()->after('admin_notes');
            $table->foreign('lpout_id')
                ->references('id')
                ->on('lpoouts')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['lpout_id']);
            $table->dropColumn('lpout_id');
        });
    }
}
