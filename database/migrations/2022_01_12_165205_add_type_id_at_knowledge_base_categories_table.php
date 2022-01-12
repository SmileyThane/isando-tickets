<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeIdAtKnowledgeBaseCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kb_categories', static function (Blueprint $table) {
            $table->unsignedInteger('type_id')->default(1);
        });
        Schema::table('kb_articles', static function (Blueprint $table) {
            $table->unsignedInteger('type_id')->default(1);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kb_categories', static function (Blueprint $table) {
            $table->dropColumn('type_id');
        });
        Schema::table('kb_articles', static function (Blueprint $table) {
            $table->dropColumn('type_id');
        });
    }
}
