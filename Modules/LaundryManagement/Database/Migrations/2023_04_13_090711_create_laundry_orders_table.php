<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaundryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('deliveryman_id')->nullable();
            $table->foreignId('pickup_deliveryman_id')->nullable();
            $table->foreignId('zone_id')->nullable();
            $table->string('order_status')->default('pending');
            $table->decimal('order_amount', 10, 2)->default(0);
            $table->string('payment_status')->default('unpaid');
            $table->string('payment_method',30)->nullable();
            $table->string('transaction_reference',50)->nullable();
            $table->point('pickup_coordinates')->nullable();
            $table->json('pickup_address')->nullable();
            $table->json('destination_address')->nullable();
            $table->point('destination_coordinates')->nullable();
            $table->string('laundry_delivery_type_id')->nullable();
            $table->integer('delivery_time')->nullable(); //delivery_type duration in day
            $table->decimal('delivery_charge', 10,2)->nullable(); 
            $table->string('bar_code')->nullable();
            $table->decimal('tax_amount',10,2)->default(0);
            $table->decimal('discount_amount',10,2)->default(0);
            $table->string('distance')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('pickup_schedule_at')->nullable();
            $table->timestamp('delivery_schedule_at')->nullable();
            $table->timestamp('pending')->nullable();
            $table->timestamp('confirmed')->nullable();
            $table->timestamp('out_for_pickup')->nullable();
            $table->timestamp('picked_up')->nullable();
            $table->timestamp('arrived')->nullable();
            $table->timestamp('processing')->nullable();
            $table->timestamp('ready_for_delivery')->nullable();
            $table->timestamp('out_for_delivery')->nullable();
            $table->timestamp('delivered')->nullable();
            $table->timestamp('cancelled')->nullable();
            $table->string('pickup_picture', 191)->nullable();
            $table->string('delivery_picture', 191)->nullable();
            $table->boolean('checked')->default(0);
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
        Schema::dropIfExists('laundry_orders');
    }
}
