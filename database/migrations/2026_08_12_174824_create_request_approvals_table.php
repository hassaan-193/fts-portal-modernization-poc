<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per (request, approver) decision backing the two-person approval rule on
 * Staff Requests and Labor Requests. The derived outcome is written back onto the
 * parent request's existing `status` column, so screens that already read `status`
 * keep working unchanged.
 */
class CreateRequestApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('request_approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('request_type', 20);          // 'staff' | 'labor'
            $table->unsignedInteger('request_id');
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('decision');      // 1 = approved, 2 = rejected
            $table->text('note')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            // One standing decision per approver per request.
            $table->unique(['request_type', 'request_id', 'user_id'], 'request_approvals_unique_decision');
            $table->index(['request_type', 'request_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('request_approvals');
    }
}
