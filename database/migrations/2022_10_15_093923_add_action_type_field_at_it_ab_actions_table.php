<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActionTypeFieldAtItAbActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_reporting_action_board_has_actions', static function (Blueprint $table) {
            $table->string('action_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_reporting_action_board_has_actions', static function (Blueprint $table) {
            $table->dropColumn('action_type');
        });
    }
}
