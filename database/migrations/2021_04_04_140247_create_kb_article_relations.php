<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKbArticleRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kb_article_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('kb_articles');
            $table->foreignId('next_article_id')->constrained('kb_articles');
            $table->unsignedSmallInteger('position')->autoIncrement();
            $table->unsignedTinyInteger('relation_type')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kb_article_relations');
    }
}
