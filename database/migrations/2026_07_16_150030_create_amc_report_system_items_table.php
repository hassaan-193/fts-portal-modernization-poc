<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmcReportSystemItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amc_report_system_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('system_key', 50);
            $table->string('item_slug', 191);
            $table->string('item_label', 255);
            $table->timestamps();

            $table->unique(['system_key', 'item_slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amc_report_system_items');
    }
}
