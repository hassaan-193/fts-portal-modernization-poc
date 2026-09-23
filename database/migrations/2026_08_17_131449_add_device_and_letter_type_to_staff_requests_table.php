<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceAndLetterTypeToStaffRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_requests', function (Blueprint $table) {
            $table->string('device_type')->nullable()->after('advance_money');
            $table->string('letter_type')->nullable()->after('device_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_requests', function (Blueprint $table) {
            $table->dropColumn(['device_type', 'letter_type']);
        });
    }
}
