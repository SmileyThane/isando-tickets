<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLangIdTagIdAtTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tags', static function (Blueprint $table) {
            $table->string('lang')->default('default')->after('name');
            $table->unsignedBigInteger('tag_id')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', static function (Blueprint $table) {
            $table->dropColumn('tag_id');
            $table->dropColumn('lang');
        });
    }
}
