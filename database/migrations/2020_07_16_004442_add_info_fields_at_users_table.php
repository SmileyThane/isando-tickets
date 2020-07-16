<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoFieldsAtUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('surname', 100)->nullable();
            $table->string('title_before_name', 20)->nullable();
            $table->string('title', 10)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('anredeform', 10)->nullable();
            $table->string('lang', 100)->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('surname');
            $table->dropColumn('title_before_name');
            $table->dropColumn('title');
            $table->dropColumn('country');
            $table->dropColumn('anredeform');
            $table->dropColumn('lang');
        });
    }
}
