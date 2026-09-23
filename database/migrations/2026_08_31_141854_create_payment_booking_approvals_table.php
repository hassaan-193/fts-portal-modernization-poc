<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per (booking, level) decision in the sequential two-level review.
 *
 * Level 1 = verifier, level 2 = approver. A booking only reaches level 2 once
 * level 1 has approved it, so at most two rows exist per booking. The rows are
 * kept even after a re-submit following a rejection — the unique key is dropped
 * and re-inserted by the service so the chain always reflects the current cycle.
 */
class CreatePaymentBookingApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payment_booking_approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_booking_id');
            $table->unsignedTinyInteger('level');          // 1 = verify, 2 = approve
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('decision');       // 1 approved, 2 rejected, 3 hold
            $table->text('note')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            // One standing decision per level per booking.
            $table->unique(['payment_booking_id', 'level'], 'payment_booking_approvals_unique_level');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('payment_booking_approvals');
    }
}
