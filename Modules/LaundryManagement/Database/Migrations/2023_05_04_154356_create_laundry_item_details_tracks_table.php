<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaundryItemDetailsTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laundry_item_details_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_order_detail_id');
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
        Schema::dropIfExists('laundry_item_details_tracks');
    }
}
