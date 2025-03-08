<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kb_articles', function (Blueprint $table) {
            $table->string('name_it')->nullable();
            $table->string('summary_it')->nullable();
            $table->string('content_it')->nullable();
            $table->string('keywords_it')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kb_articles', function (Blueprint $table) {
            $table->dropColumn('name_it');
            $table->dropColumn('summary_it');
            $table->dropColumn('content_it');
            $table->dropColumn('keywords_it');
        });
    }
};
