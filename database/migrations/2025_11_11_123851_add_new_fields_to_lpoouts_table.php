<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToLpooutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lpoouts', function (Blueprint $table) {
            $table->string('trn_no')->nullable()->after('vendor_id');
            $table->string('kindly_attn')->nullable()->after('trn_no');
            $table->string('payment_type')->nullable()->after('amount');
            $table->date('cheque_date')->nullable()->after('payment_type');
            $table->json('items')->nullable()->after('cheque_date');
            $table->enum('status', ['Pending', 'Approved', 'Not Approved'])->default('Pending')->after('items');

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
            $table->dropColumn(['trn_no', 'kindly_attn', 'payment_type', 'cheque_date', 'items']);
        });
    }
}