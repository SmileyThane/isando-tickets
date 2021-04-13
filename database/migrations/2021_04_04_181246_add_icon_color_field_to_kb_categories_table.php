<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIconColorFieldToKbCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kb_categories', static function (Blueprint $table) {
            $table->string('icon_color')->default('#9e9e9e');
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
            $table->dropColumn('icon_color');
        });
    }
}
