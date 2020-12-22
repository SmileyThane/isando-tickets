<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserMergeFieldsAtTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', static function (Blueprint $table) {
            $table->bigInteger('unifier_id')->nullable();
            $table->dateTime('merged_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', static function (Blueprint $table) {
            $table->dropColumn('unifier_id');
            $table->dropColumn('merged_at');

        });
    }
}
