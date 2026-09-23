<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSentBackFieldsToPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Step 2 -> Step 1 return trip. `department_status`/`status` become
            // 'Sent Back'; these columns keep the reason and the audit trail so the
            // requester still sees why after they resubmit.
            $table->text('sent_back_notes')->nullable()->after('department_notes');
            $table->timestamp('sent_back_at')->nullable()->after('sent_back_notes');
            $table->bigInteger('sent_back_by')->nullable()->after('sent_back_at');
            $table->unsignedInteger('sent_back_count')->default(0)->after('sent_back_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['sent_back_notes', 'sent_back_at', 'sent_back_by', 'sent_back_count']);
        });
    }
}
