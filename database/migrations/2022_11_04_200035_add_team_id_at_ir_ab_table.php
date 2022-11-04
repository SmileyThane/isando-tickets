<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdAtIrAbTable extends Migration
{
    public function up()
    {
        Schema::table('incident_reporting_action_boards', static function (Blueprint $table) {
            $table->bigInteger('team_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_reporting_action_boards', static function (Blueprint $table) {
            $table->dropColumn('team_id');
        });
    }
}
