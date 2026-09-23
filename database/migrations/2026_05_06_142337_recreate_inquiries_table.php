<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateInquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('inquiries_status_history');
        Schema::dropIfExists('inquiries');

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_no')->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('client_name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('location')->nullable();
            $table->enum('inquiry_type', ['Project', 'AMC', 'Installation', 'Other']);
            $table->string('other_type')->nullable();
            $table->enum('source', ['Call', 'Email', 'Walk-in', 'Website', 'Referral']);
            $table->decimal('expected_price', 15, 2)->nullable();
            $table->enum('status', ['New', 'Assigned', 'Under Review', 'Site Visit Pending', 'Site Visit Done', 'Sent to Sales', 'Quotation Created', 'Under Follow-up', 'Won', 'Lost', 'Closed'])->default('New');
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->date('follow_up_date')->nullable();
            $table->date('expected_closing_date')->nullable();
            $table->string('assigned_department')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
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
        Schema::dropIfExists('inquiries');
    }
}
