<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrStaffAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('qr_staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('scanned_by');
            $table->unsignedBigInteger('site_id')->nullable();
            $table->string('custom_site_name')->nullable();
            $table->date('attendance_date');
            $table->dateTime('check_in_time');
            $table->dateTime('check_out_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('overtime_minutes')->default(0);
            $table->time('shift_end_time')->default('17:00:00');
            $table->enum('status', ['checked_in', 'checked_out'])->default('checked_in');

            $table->enum('review_status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            $table->enum('final_overtime_source', ['manual', 'qr', 'custom'])->nullable();
            $table->integer('final_overtime_minutes')->nullable();
            $table->unsignedBigInteger('finalized_by')->nullable();
            $table->dateTime('finalized_at')->nullable();
            $table->text('final_decision_notes')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('staff_id');
            $table->index('scanned_by');
            $table->index('attendance_date');
            $table->index(['staff_id', 'attendance_date']);
            $table->index('status');
            $table->index('review_status');
            $table->index('reviewed_by');
            $table->index('final_overtime_source');
            $table->index('finalized_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('qr_staff_attendances');
    }
}
