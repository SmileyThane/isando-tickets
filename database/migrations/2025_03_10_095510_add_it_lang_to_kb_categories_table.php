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
        Schema::table('kb_categories', function (Blueprint $table) {
            $table->string('name_it')->nullable();
            $table->string('description_it')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kb_categories', function (Blueprint $table) {
            $table->dropColumn('name_it');
            $table->dropColumn('description_it');
        });
    }
};
