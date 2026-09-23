<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTermsToLpooutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lpoouts', function (Blueprint $table) {
            $table->longText('terms')->nullable()->after('amount'); // adjust 'after' as needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lpoouts', function (Blueprint $table) {
            $table->dropColumn('terms');
        });
    }
}
// Source - https://stackoverflow.com/a
// Posted by AddWeb Solution Pvt Ltd, modified by community. See post 'Timeline' for change history
// Retrieved 2025-11-19, License - CC BY-SA 4.0

