<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserBattleLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->float('win_rate')->default(0);
            $table->integer('win')->default(0);
            $table->integer('lose')->default(0);
            $table->integer('draw')->default(0);
            $table->dateTime('battle_logs_updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('win_rate');
            $table->dropColumn('win');
            $table->dropColumn('lose');
            $table->dropColumn('draw');
            $table->dropColumn('battle_logs_updated_at');
        });
    }
}
