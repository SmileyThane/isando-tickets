<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIrFieldsAtIrAbTable extends Migration
{
    public function up()
    {
        Schema::table('incident_reporting_action_boards', static function (Blueprint $table) {
            $table->text('source')->nullable();
            $table->dateTime('occurred_on')->nullable();
            $table->dateTime('detected_on')->nullable();
            $table->dateTime('reported_on')->nullable();
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
            $table->dropColumn('source');
            $table->dropColumn('occurred_on');
            $table->dropColumn('detected_on');
            $table->dropColumn('reported_on');
        });
    }
}
