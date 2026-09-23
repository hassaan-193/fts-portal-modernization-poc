<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobileNoToStafProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staf_profile', function (Blueprint $table) {
            $table->string('mobile_no')->nullable();   // Add mobile_no column
            $table->string('home_mobile_no')->nullable();  // Add home_mobile_no column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staf_profile', function (Blueprint $table) {
            $table->dropColumn('mobile_no');
            $table->dropColumn('home_mobile_no');
        });
    }
}


