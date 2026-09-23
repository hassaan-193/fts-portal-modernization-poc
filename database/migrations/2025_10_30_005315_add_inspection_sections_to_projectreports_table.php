<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInspectionSectionsToProjectreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
Schema::table('projectreports', function (Blueprint $table) {
        $table->json('fire_alarm_data')->nullable();
        $table->json('fire_fighting_data')->nullable();
        $table->json('fm200_data')->nullable();
        $table->json('foam_tank_data')->nullable();
        $table->json('voice_evacuation_data')->nullable();
        $table->json('emergency_lighting_data')->nullable();
        $table->json('system_interfacing_data')->nullable();
        $table->json('exit_route_data')->nullable();
        $table->json('storage_conditions_data')->nullable();
        $table->json('pump_data')->nullable();
        $table->json('deluge_data')->nullable();
        $table->json('urgent_summary')->nullable();
        $table->json('photos_data')->nullable();
        $table->json('scope_data')->nullable();
        $table->json('next_due_dates')->nullable();
        $table->text('notes_used_items')->nullable();
        $table->date('next_inspection_due')->nullable();
        $table->date('expiry_update_required_on')->nullable();
        $table->string('client_eid_details')->nullable();
        $table->string('client_phone')->nullable();
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
        $table->dropColumn([
            'fire_alarm_data',
            'fire_fighting_data',
            'fm200_data',
            'foam_tank_data',
            'voice_evacuation_data',
            'emergency_lighting_data',
            'system_interfacing_data',
            'exit_route_data',
            'storage_conditions_data',
            'pump_data',
            'deluge_data',
            'urgent_summary',
            'photos_data',
            'scope_data',
            'next_due_dates',
            'notes_used_items',
        ]);
    });
    }
}