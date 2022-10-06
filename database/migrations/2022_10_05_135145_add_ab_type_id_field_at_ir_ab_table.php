<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAbTypeIdFieldAtIrAbTable extends Migration
{
    public function up()
    {
        Schema::table('incident_reporting_action_boards', static function (Blueprint $table) {
            $table->integer('type_id')->default(1);
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
            $table->dropColumn('type_id');
        });
    }
}
