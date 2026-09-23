<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateInquiryRoutingConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiry_routing_configs', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_type')->unique();
            $table->string('department');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('inquiry_routing_configs')->insert([
            ['inquiry_type' => 'AMC', 'department' => 'Maintenance Team', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['inquiry_type' => 'Project', 'department' => 'Project Team', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['inquiry_type' => 'Installation', 'department' => 'Project Team', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['inquiry_type' => 'Other', 'department' => 'Project Team', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiry_routing_configs');
    }
}
