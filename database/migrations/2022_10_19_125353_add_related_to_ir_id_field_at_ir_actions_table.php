<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelatedToIrIdFieldAtIrActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_reporting_actions', static function (Blueprint $table) {
            $table->bigInteger('related_to_ir_ab_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_reporting_actions', static function (Blueprint $table) {
            $table->dropColumn('related_to_ir_ab_id');
        });
    }
}
