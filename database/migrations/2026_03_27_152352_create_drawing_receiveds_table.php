<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrawingReceivedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawing_receiveds', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lpoin_id');
            $table->unsignedBigInteger('responsible_engineer_id');
            $table->string('type_of_work');
            $table->date('start_date');
            $table->date('review_comments_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->string('status')->default('Under Review');
            $table->longText('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('lpoin_id')->references('id')->on('lpoins')->onDelete('cascade');
            $table->foreign('responsible_engineer_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('lpoin_id');
            $table->index('responsible_engineer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drawing_receiveds');
    }
}
