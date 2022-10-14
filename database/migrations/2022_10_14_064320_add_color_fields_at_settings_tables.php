<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorFieldsAtSettingsTables extends Migration
{
    public function up()
    {
        Schema::table('incident_reporting_focus_priorities', static function (Blueprint $table) {
            $table->string('color')->nullable();
        });

        Schema::table('incident_reporting_impact_potentials', static function (Blueprint $table) {
            $table->string('color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_reporting_focus_priorities', static function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('incident_reporting_impact_potentials', static function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}
