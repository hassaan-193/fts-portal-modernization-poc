<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorPayableFieldsToPaymentInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_invoices', 'type'))
                $table->string('type')->nullable()->after('id');
            if (!Schema::hasColumn('payment_invoices', 'vendor_id'))
                $table->unsignedBigInteger('vendor_id')->nullable()->after('lpoout_id');
            if (!Schema::hasColumn('payment_invoices', 'project_id'))
                $table->unsignedBigInteger('project_id')->nullable()->after('vendor_id');
            if (!Schema::hasColumn('payment_invoices', 'is_historical'))
                $table->boolean('is_historical')->default(false)->after('status');
            if (!Schema::hasColumn('payment_invoices', 'source_reference'))
                $table->string('source_reference')->nullable()->after('is_historical');
            if (!Schema::hasColumn('payment_invoices', 'source_date'))
                $table->date('source_date')->nullable()->after('source_reference');
            if (!Schema::hasColumn('payment_invoices', 'historical_party'))
                $table->string('historical_party')->nullable()->after('source_date');
            if (!Schema::hasColumn('payment_invoices', 'historical_project'))
                $table->string('historical_project')->nullable()->after('historical_party');
            if (!Schema::hasColumn('payment_invoices', 'created_by'))
                $table->unsignedBigInteger('created_by')->nullable()->after('historical_project');
            if (!Schema::hasColumn('payment_invoices', 'document_path'))
                $table->string('document_path')->nullable()->after('created_by');
        });
    }

    public function down()
    {
        Schema::table('payment_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'vendor_id',
                'project_id',
                'is_historical',
                'source_reference',
                'source_date',
                'historical_party',
                'historical_project',
                'created_by',
                'document_path',
            ]);
        });
    }
}
