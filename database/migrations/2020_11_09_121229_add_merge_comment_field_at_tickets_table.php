<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMergeCommentFieldAtTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', static function (Blueprint $table) {
            $table->longText('merge_comment')->nullable();
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
            $table->dropColumn('merge_comment');
        });
    }
}
