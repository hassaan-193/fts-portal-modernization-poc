<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceFieldsToBalanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('balance_transactions', 'reference_type')) {
                $table->string('reference_type')->nullable()->index();
            }
            if (!Schema::hasColumn('balance_transactions', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->dropIndex(['reference_type']); // drop index first (optional but cleaner)
            $table->dropIndex(['reference_id']);

            $table->dropColumn(['reference_type', 'reference_id']);
        });
    }
}
