<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefineKbArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kb_articles', static function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->boolean('is_internal')->default(0);
            $table->string('featured_color')->default('transparent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kb_articles', static function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('kb_categories');
            $table->dropColumn('is_internal');
            $table->dropForeign(['prev_id']);
            $table->dropColumn('featured_color');
        });
    }
}
