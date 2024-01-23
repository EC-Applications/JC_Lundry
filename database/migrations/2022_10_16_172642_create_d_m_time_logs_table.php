<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDMTimeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('d_m_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_man_id');
            $table->foreignId('zone_id');
            $table->foreignId('shift_id');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('duration', 23, 3)->nullable();
            $table->string('status')->nullable();
            $table->date('date')->nullable();
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
        Schema::dropIfExists('d_m_time_logs');
    }
}
