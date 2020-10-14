<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIconFieldAtAddressTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('address_types', static function (Blueprint $table) {
            $table->string('icon')->nullable()->default('mdi-map-marker');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('address_types', static function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
}
