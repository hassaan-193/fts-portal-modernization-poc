<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * AMC report drafts.
 *
 * A report can be saved as 'draft' by the user filling in the visit form and finished
 * later; only on final submit does it become 'pending' and reach the report-status page.
 * `created_by` ties each draft to the user who started it.
 *
 * site_location / site_name become nullable so a partly filled draft can be saved —
 * the form and its validation already treat both as optional.
 */
class AddDraftSupportToProjectreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `projectreports` MODIFY `status` ENUM('draft','pending','approved','disapproved') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE `projectreports` MODIFY `site_location` VARCHAR(191) NULL");
        DB::statement("ALTER TABLE `projectreports` MODIFY `site_name` VARCHAR(191) NULL");

        Schema::table('projectreports', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('status');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projectreports', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropColumn('created_by');
        });

        // Unfinished drafts are kept rather than deleted: they fall back into the approval queue.
        DB::statement("UPDATE `projectreports` SET `status` = 'pending' WHERE `status` = 'draft'");
        DB::statement("ALTER TABLE `projectreports` MODIFY `status` ENUM('pending','approved','disapproved') NOT NULL DEFAULT 'pending'");

        DB::statement("UPDATE `projectreports` SET `site_location` = '' WHERE `site_location` IS NULL");
        DB::statement("UPDATE `projectreports` SET `site_name` = '' WHERE `site_name` IS NULL");
        DB::statement("ALTER TABLE `projectreports` MODIFY `site_location` VARCHAR(191) NOT NULL");
        DB::statement("ALTER TABLE `projectreports` MODIFY `site_name` VARCHAR(191) NOT NULL");
    }
}
