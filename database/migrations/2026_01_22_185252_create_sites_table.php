<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            
            // Site Name
            $table->string('site_name')->unique();
            
            // Engineer Reference
            $table->foreignId('engineer_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('engineer_id');
            $table->index('site_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sites');
    }
}
