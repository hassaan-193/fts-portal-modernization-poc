<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->enum('country', ['UAE', 'International']);
            $table->string('city')->nullable(); // Only applicable if UAE is selected
            $table->string('work_type');
            $table->enum('installation', ['Annual Maintenance', 'Supply']);
            $table->string('phone_no');
            $table->string('email');
            $table->text('message');
            $table->enum('status', ['open', 'success', 'fail'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
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
