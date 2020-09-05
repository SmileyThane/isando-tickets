<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCountryFieldsAtAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'country'))
        {
            Schema::table('users', static function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }
        Schema::table('users', static function (Blueprint $table) {
            $table->bigInteger('country_id')->nullable();
        });
        if (Schema::hasColumn('companies', 'country'))
        {
            Schema::table('companies', static function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }
        Schema::table('companies', static function (Blueprint $table) {
            $table->bigInteger('country_id')->nullable();
        });
        if (Schema::hasColumn('clients', 'country'))
        {
            Schema::table('clients', static function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }
        Schema::table('clients', static function (Blueprint $table) {
            $table->bigInteger('country_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->longText('country')->nullable();
        });
        Schema::table('companies', static function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->longText('country')->nullable();
        });
        Schema::table('clients', static function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->longText('country')->nullable();
        });

    }
}
