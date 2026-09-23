<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per device a user has signed in on.
 *
 * `users.firebase_token` is a single column, which breaks in two ways: signing in
 * on a second phone silently kills notifications on the first, and there is
 * nowhere to record that FCM rejected a token so it can be dropped. Both matter
 * for an approval workflow where a missed notification means a payment sits
 * unreviewed.
 *
 * The old column is left alone — the attendance app still writes to it.
 */
class CreateDeviceTokensTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');

            // FCM registration tokens are long and have no documented maximum;
            // 512 is comfortably above what is issued in practice.
            $table->string('token', 512);
            $table->string('platform', 20)->default('android');

            // Helps identify which phone a token belongs to when revoking.
            $table->string('device_name')->nullable();

            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });

        // A token identifies one install, so it must map to exactly one user —
        // re-registering after a handover reassigns rather than duplicating.
        // Indexed on a prefix because MySQL caps key length at 191 chars for
        // utf8mb4, and the column is 512.
        \DB::statement('CREATE UNIQUE INDEX device_tokens_token_unique ON device_tokens (token(191))');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('device_tokens');
    }
}
