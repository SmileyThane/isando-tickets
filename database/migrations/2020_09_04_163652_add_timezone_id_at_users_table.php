<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimezoneIdAtUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->bigInteger('timezone_id')->default(35);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn('timezone_id');
        });
    }
}
