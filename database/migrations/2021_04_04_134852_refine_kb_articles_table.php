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
            $table->text('keywords')->nullable();
            $table->text('keywords_de')->nullable();
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
            $table->dropColumn('featured_color');
            $table->dropColumn('keywords');
            $table->dropColumn('keywords_de');
        });
    }
}
