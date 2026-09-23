<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExcludeFromExpiryToStafProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staf_profile', function (Blueprint $table) {
            $table->boolean('exclude_from_expiry')->default(false);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expiry_to_staf_profile', function (Blueprint $table) {
            $table->dropColumn('exclude_from_expiry');
        });
    }
}
