<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->unsignedInteger('labor_id')->nullable();
            $table->foreign('labor_id')
                ->references('id')
                ->on('staf_profile')
                ->onDelete('cascade');

            $table->foreignId('foreman_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            
            // Data Columns
            $table->date('attendance_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamp('marked_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Indexes & Constraints
            $table->unique(['labor_id', 'attendance_date']);
            $table->index('status');
            $table->index('attendance_date');
            $table->index('foreman_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
