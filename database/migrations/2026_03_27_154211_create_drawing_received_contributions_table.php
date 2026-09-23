<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrawingReceivedContributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawing_received_contributions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('drawing_received_id');
            $table->unsignedBigInteger('contributed_by_id');
            $table->string('contribution_type');
            $table->longText('description')->nullable();
            $table->string('status');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('drawing_received_id')->references('id')->on('drawing_receiveds')->onDelete('cascade');
            $table->foreign('contributed_by_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('drawing_received_id');
            $table->index('contributed_by_id');
            $table->index('contribution_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drawing_received_contributions');
    }
}
