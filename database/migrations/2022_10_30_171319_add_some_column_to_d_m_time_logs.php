<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToDMTimeLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('d_m_time_logs', function (Blueprint $table) {
            $table->time('shift_time_online')->nullable();
            $table->time('shift_time_offline')->nullable();
            $table->decimal('total_working_time', 23, 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('d_m_time_logs', function (Blueprint $table) {
           $table->dropColumn('shift_time_online');
           $table->dropColumn('shift_time_offline');
           $table->dropColumn('total_working_time');

        });
    }
}
