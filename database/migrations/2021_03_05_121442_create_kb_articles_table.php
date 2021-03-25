<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKbArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kb_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('category_id')->nullable()->constrained('kb_categories');
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->text('summary')->nullable();
            $table->text('summary_de')->nullable();
            $table->longText('content')->nullable();
            $table->longText('content_de')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('kb_articles');
    }
}
