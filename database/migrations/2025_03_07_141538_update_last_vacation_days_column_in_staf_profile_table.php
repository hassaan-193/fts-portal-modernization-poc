<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLastVacationDaysColumnInStafProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staf_profile', function (Blueprint $table) {
            $table->integer('last_vacation_days')->nullable()->change();
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
            $table->integer('last_vacation_days')->nullable(false)->change();
        });
    }
}
