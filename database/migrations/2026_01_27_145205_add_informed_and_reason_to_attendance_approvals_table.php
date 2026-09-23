<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInformedAndReasonToAttendanceApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_approvals', function (Blueprint $table) {
            $table->enum('informed', ['informed', 'uninformed'])->nullable()->after('action');
            $table->string('specific_reason')->nullable()->after('informed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_approvals', function (Blueprint $table) {
            $table->dropColumn(['informed', 'specific_reason']);
        });
    }
}
