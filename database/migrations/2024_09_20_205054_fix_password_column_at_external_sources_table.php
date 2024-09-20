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
        Schema::table('external_sources', static function (Blueprint $table) {
            $table->string('password', 256)->change();
            $table->dateTime('last_billed_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('external_sources', static function (Blueprint $table) {
            $table->string('password', 191)->change();
            $table->dateTime('last_billed_at')->change();
        });
    }
};
