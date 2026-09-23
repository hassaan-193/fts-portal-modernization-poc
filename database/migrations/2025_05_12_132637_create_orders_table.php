<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('trn');
            $table->unsignedInteger('vendor_id');
            $table->string('attn')->nullable();
            $table->string('ship_to')->nullable();
            $table->text('address')->nullable();
            $table->string('contact')->nullable();
            $table->string('ref_no')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            

            // Order‐level totals & discount
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('total_after_discount', 14, 2)->default(0);
            $table->decimal('vat', 14, 2)->default(0);
            $table->decimal('total_with_vat', 14, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
