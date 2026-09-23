<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('staff_profile_id');
            $table->foreign('staff_profile_id')->references('id')->on('staf_profile')->onDelete('cascade');            
            $table->enum('type', ['warning', 'appreciation']);
            $table->string('title');
            $table->text('content');
            $table->string('issued_by');
            $table->date('issued_at');
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
        Schema::dropIfExists('letters');
    }
}
