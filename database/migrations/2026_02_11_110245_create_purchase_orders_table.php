<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->string('request_type')->default('general');
            $table->bigInteger('quotation_id')->nullable();
            $table->text('other_info')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->date('date')->nullable();
            $table->json('items')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('Pending');
            $table->string('department_status')->default('Pending');
            $table->text('department_notes')->nullable();
            $table->bigInteger('admin_id')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
