<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBattleRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battle_records', function (Blueprint $table) {
            $table->string('id');
            $table->string('first_client_id');
            $table->string('first_team_id');
            $table->string('second_client_id');
            $table->string('second_team_id');
            $table->string('winner');
            $table->string('battle_type');
            $table->string('battle_uuid');
            $table->dateTime('created_at');
            $table->json('fighters');
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
        Schema::dropIfExists('battle_records');
    }
}
