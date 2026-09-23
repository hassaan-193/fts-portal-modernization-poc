<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceLaborDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_labor_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')
                ->constrained('attendances')
                ->cascadeOnDelete();
            $table->unsignedInteger('labor_id');
            $table->foreign('labor_id')
                ->references('id')
                ->on('staf_profile')
                ->cascadeOnDelete();
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->unsignedBigInteger('site_id')->nullable();
            $table->string('custom_site_name')->nullable();
            $table->timestamps();
            $table->unique(['attendance_id', 'labor_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_labor_details');
    }
}
