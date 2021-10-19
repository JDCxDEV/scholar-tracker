<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScholarDailyRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::dropIfExists('scholar_daily_slps');

        Schema::create('scholar_daily_records', function (Blueprint $table) {
            $table->string('ronin_address')->nullable();
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('total_slp')->default(0);
            $table->integer('rank')->default(0);
            $table->integer('mmr')->default(0);
            $table->integer('total_matches')->default(0);
            $table->integer('slp')->default(0);
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scholar_daily_records');
    }
}
