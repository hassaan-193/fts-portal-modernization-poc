<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('staf_id'); // Foreign key to staf_profile
            $table->date('ticket_date');
            $table->string('agent_name');
            $table->enum('travel_type', ['One Way', 'Two Way']);
            $table->date('travel_date');
            $table->date('return_date')->nullable(); // Nullable for one-way trips
            $table->decimal('amount', 10, 2);
            $table->decimal('vat', 10, 2);
            $table->decimal('total_value', 10, 2);
            $table->string('payment_status')->nullable(); // Placeholder for future updates
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('staf_id')->references('id')->on('staf_profile')->onDelete('cascade');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
