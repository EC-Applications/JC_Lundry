<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->date('date');
            $table->time('online')->nullable();
            $table->time('offline')->nullable();
            $table->float('online_time')->default(0);
            $table->time('accepted')->nullable();
            $table->time('delivered')->nullable();
            $table->float('idle_time')->default(0);
            $table->string('on_time_delivery')->nullable()->default(0);
            $table->string('late_delivery')->nullable()->default(0);
            $table->string('late_pickup')->nullable()->default(0);
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
        Schema::dropIfExists('time_logs');
    }
}
