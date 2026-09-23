<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateLetterTypesEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify the enum type to include all new letter types
        DB::statement("ALTER TABLE letters MODIFY COLUMN type ENUM('warning', 'appreciation', 'general_notice', 'poor_performance_notice', 'accommodation_notice', 'vehicle_notice', 'attendance_notice', 'weather_notice', 'eid_holidays_notice')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE letters MODIFY COLUMN type ENUM('warning', 'appreciation')");
    }
}
