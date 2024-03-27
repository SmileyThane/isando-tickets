<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('client_filter_groups', static function (Blueprint $table) {
            $table->string('number')->after('name')->nullable();
            $table->text('description')->after('number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_filter_groups', static function (Blueprint $table) {
            $table->dropColumn('number');
            $table->dropColumn('description');
        });

    }
};
