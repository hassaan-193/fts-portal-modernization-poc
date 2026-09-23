<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInquiryDepartmentReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiry_department_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->unique()->constrained('inquiries')->onDelete('cascade');
            $table->string('assigned_department');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assignment_date')->nullable();
            $table->string('current_status')->nullable();
            $table->text('internal_comments')->nullable();
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->timestamp('response_deadline')->nullable();
            $table->string('technical_review_status')->nullable();
            $table->boolean('site_visit_required')->default(false);
            $table->date('proposed_visit_date')->nullable();
            $table->foreignId('visit_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('visit_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('inquiry_department_reviews');
    }
}
