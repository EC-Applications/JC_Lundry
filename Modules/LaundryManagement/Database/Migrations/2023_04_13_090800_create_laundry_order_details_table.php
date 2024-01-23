<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaundryOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laundry_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_orders_id');
            $table->foreignId('services_id');
            $table->foreignId('laundry_item_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price',10,2)->default(0);
            $table->string('bar_code')->nullable();
            $table->timestamp('processing')->nullable();
            $table->timestamp('processed')->nullable();
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
        Schema::dropIfExists('laundry_order_details');
    }
}
