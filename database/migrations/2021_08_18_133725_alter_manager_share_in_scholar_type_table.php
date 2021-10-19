<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterManagerShareInScholarTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scholar_types', function (Blueprint $table) {
            $table->bigInteger('manager_share')->nullable()->default(0)->change();
            $table->bigInteger('scholar_share')->nullable()->default(0)->change();
            $table->bigInteger('slp_required')->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scholar_types', function (Blueprint $table) {
            //
        });
    }
}
