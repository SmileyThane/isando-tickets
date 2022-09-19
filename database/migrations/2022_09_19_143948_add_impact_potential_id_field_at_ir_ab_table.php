<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImpactPotentialIdFieldAtIrAbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_reporting_action_boards', static function (Blueprint $table) {
            $table->bigInteger('impact_potential_id')->nullable();
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
            $table->dropColumn('impact_potential_id');
        });
    }
}
