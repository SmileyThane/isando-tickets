<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentReportingActionBoardHasActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_reporting_action_board_has_actions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('action_board_id');
            $table->bigInteger('action_id');
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
        Schema::dropIfExists('incident_reporting_action_board_has_actions');
    }
}