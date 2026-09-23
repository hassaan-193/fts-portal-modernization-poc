<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Links a system login to the staff profile it belongs to, so the self-service
 * request form can resolve the requester's staf_id server-side instead of
 * trusting a value posted by the browser.
 *
 * Nullable and on `users` (rather than a user_id on staf_profile) because most
 * staff have no login at all — the FK belongs on the side that is the exception.
 */
class AddStafProfileIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // unsignedInteger, not unsignedBigInteger: staf_profile.id is increments().
            $table->unsignedInteger('staf_profile_id')->nullable()->after('api_token');
            $table->foreign('staf_profile_id')->references('id')->on('staf_profile')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['staf_profile_id']);
            $table->dropColumn('staf_profile_id');
        });
    }
}
