<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmergencyVisitFieldsToProjectreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projectreports', function (Blueprint $table) {
            $table->boolean('is_emergency_visit')->default(false)->after('visit_schedule_id');
            $table->date('emergency_visit_date')->nullable()->after('is_emergency_visit');
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
            $table->dropColumn(['is_emergency_visit', 'emergency_visit_date']);
        });
    }
}
