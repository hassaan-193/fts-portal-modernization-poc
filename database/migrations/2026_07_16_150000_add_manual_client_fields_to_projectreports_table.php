<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManualClientFieldsToProjectreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projectreports', function (Blueprint $table) {
            $table->boolean('is_manual_client')->default(false)->after('company_id');
            $table->string('manual_client_name')->nullable()->after('is_manual_client');
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
            $table->dropColumn(['is_manual_client', 'manual_client_name']);
        });
    }
}
