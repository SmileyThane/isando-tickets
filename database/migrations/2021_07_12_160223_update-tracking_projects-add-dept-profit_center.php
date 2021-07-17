<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTrackingProjectsAddDeptProfitCenter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_projects', static function (Blueprint $table) {
            $table->renameColumn('name', 'project');
            $table->string('department', 255)->nullable();
            $table->string('profit_center', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_projects', static function (Blueprint $table) {
            $table->renameColumn('project', 'name');
            $table->dropColumn('department');
            $table->dropColumn('profit_center');
        });
    }
}
