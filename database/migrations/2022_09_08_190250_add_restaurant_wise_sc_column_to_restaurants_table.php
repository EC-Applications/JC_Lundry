<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRestaurantWiseScColumnToRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->boolean('restaurant_wise_sc_status')->default(0);
            $table->decimal('minimum_shipping_charge', 23, 3)->nullable();
            $table->decimal('per_km_shipping_charge', 23, 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('restaurant_wise_sc_status');
            $table->dropColumn('minimum_shipping_charge');
            $table->dropColumn('per_km_shipping_charge');
        });
    }
}
