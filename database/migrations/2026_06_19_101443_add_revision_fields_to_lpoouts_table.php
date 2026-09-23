<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevisionFieldsToLpooutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lpoouts', function (Blueprint $table) {
            $table->integer('revision_number')->default(1)->after('status');
            $table->unsignedBigInteger('parent_lpoout_id')->nullable()->after('revision_number');
            $table->boolean('is_latest_revision')->default(true)->after('parent_lpoout_id');
            $table->unsignedBigInteger('revised_by')->nullable()->after('is_latest_revision');
            $table->text('revision_reason')->nullable()->after('revised_by');
            $table->timestamp('revised_at')->nullable()->after('revision_reason');
        });
    }

    public function down()
    {
        Schema::table('lpoouts', function (Blueprint $table) {
            $table->dropColumn([
                'revision_number',
                'parent_lpoout_id',
                'is_latest_revision',
                'revised_by',
                'revision_reason',
                'revised_at',
            ]);
        });
    }
}
