<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAutoAssignNewEntityFieldAtLimitationGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('limitation_groups', static function (Blueprint $table) {
            $table->boolean('is_auto_assign_new_entities')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('limitation_groups', static function (Blueprint $table) {
            $table->dropColumn('number');
        });
    }
}
