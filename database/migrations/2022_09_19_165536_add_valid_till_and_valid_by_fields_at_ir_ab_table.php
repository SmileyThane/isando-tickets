<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidTillAndValidByFieldsAtIrAbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_reporting_action_boards', static function (Blueprint $table) {
            $table->dateTime('valid_till')->nullable();
            $table->bigInteger('updated_by')->nullable();
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
            $table->dropColumn('valid_till');
            $table->dropColumn('updated_by');
        });
    }
}
