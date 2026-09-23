<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlockInfoToProjectreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projectreports', function (Blueprint $table) {
                $table->json('block_info')->nullable()->after('required_items');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projectreports', function (Blueprint $table) {
                $table->dropColumn('block_info');
            });
    }
}
