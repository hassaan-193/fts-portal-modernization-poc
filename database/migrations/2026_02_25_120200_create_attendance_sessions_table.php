<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('session_date');
            
            // Clock-in details
            $table->dateTime('clock_in_time');
            $table->decimal('clock_in_latitude', 10, 8);
            $table->decimal('clock_in_longitude', 11, 8);
            $table->decimal('clock_in_distance_meters', 8, 2)->nullable();
            
            // Clock-out details
            $table->dateTime('clock_out_time')->nullable();
            $table->decimal('clock_out_latitude', 10, 8)->nullable();
            $table->decimal('clock_out_longitude', 11, 8)->nullable();
            $table->decimal('clock_out_distance_meters', 8, 2)->nullable();
            
            // Session duration
            $table->integer('duration_minutes')->nullable();
            
            // Shift and status
            $table->enum('shift_window', ['shift_1', 'shift_2']);
            $table->boolean('is_late')->default(false);
            $table->enum('work_mode', ['split_shift', 'continuous'])->default('split_shift');
            $table->enum('session_status', ['open', 'closed'])->default('open');
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for efficient queries
            $table->index('user_id');
            $table->index('session_date');
            $table->index(['user_id', 'session_date']);
            $table->index('shift_window');
            $table->index('is_late');
            $table->index('session_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_sessions');
    }
}
