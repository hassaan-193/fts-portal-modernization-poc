<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInquiryEngineerReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiry_engineer_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->unique()->constrained('inquiries')->onDelete('cascade');
            $table->boolean('visit_completed')->default(false);
            $table->text('site_condition_notes')->nullable();
            $table->text('scope_understanding')->nullable();
            $table->text('materials_required')->nullable();
            $table->text('challenges_risks')->nullable();
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('inquiry_engineer_reports');
    }
}
