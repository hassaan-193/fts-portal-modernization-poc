<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Payment bookings — one row per cheque or cash payment recorded by an accountant.
 *
 * A single table backs both booking types; the type-specific columns (cheque_* /
 * cash_*) are nullable and only one set is ever populated, decided by `booking_type`.
 * That keeps the module self-contained: every counterparty field (payee, project,
 * bank account) is free text, so nothing here joins to the older finance tables.
 *
 * `status` carries the sequential two-level review outcome. The per-approver detail
 * lives in payment_booking_approvals; this column is the denormalised current state
 * so lists and the mobile API never have to aggregate the chain to render a badge.
 */
class CreatePaymentBookingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payment_bookings', function (Blueprint $table) {
            $table->increments('id');

            // CHQ-2026-0085 / CSH-2026-0085 — generated on submit, never edited.
            $table->string('reference_no', 40)->unique();
            $table->string('booking_type', 10);               // 'cheque' | 'cash'

            // --- Payment information (shared by both types) ---
            $table->string('payee');                          // "Payee / Beneficiary" (cheque) or "Paid to" (cash)
            $table->string('payment_against')->nullable();
            $table->text('purpose')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('project_cost_centre')->nullable();
            $table->date('booking_date')->nullable();

            // --- Cheque details (booking_type = cheque) ---
            $table->string('cheque_number', 60)->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_account')->nullable();
            $table->date('release_date')->nullable();

            // --- Cash details (booking_type = cash) ---
            $table->string('cash_account')->nullable();
            $table->date('payment_date')->nullable();

            // --- Sequential review state (see App\Models\PaymentBooking constants) ---
            $table->unsignedTinyInteger('status')->default(0); // 0 Draft .. 5 On Hold
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();      // level 1 cleared
            $table->timestamp('approved_at')->nullable();      // level 2 cleared
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('booking_type');
            $table->index('status');
            $table->index('created_by');
            // The release/payment dates drive every "released this month" figure.
            $table->index('release_date');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('payment_bookings');
    }
}
