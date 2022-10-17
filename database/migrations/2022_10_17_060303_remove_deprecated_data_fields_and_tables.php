<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDeprecatedDataFieldsAndTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('incident_reporting_action_board_categories');
        Schema::dropIfExists('incident_reporting_action_board_priorities');
        Schema::dropIfExists('incident_reporting_action_board_states');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
