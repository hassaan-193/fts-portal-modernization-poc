<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInquiryQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiry_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->unique()->constrained('inquiries')->onDelete('cascade');
            $table->decimal('quotation_amount', 15, 2)->nullable();
            $table->text('scope_of_work')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->date('validity_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiry_quotations');
    }
}
