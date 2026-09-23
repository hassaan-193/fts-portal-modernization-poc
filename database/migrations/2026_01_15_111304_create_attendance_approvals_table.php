<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_approvals', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('attendance_id')
                ->constrained('attendances')
                ->onDelete('cascade');
            
            $table->foreignId('approved_by')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->enum('action', ['approved', 'rejected']);
            
            $table->text('reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('attendance_id');
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_approvals');
    }
}
