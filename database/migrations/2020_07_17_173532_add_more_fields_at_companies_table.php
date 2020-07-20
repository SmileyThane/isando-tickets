<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsAtCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('city', 100)->nullable();
            $table->string('country', 500)->nullable();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('city', 100)->nullable();
            $table->string('country', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('city');
            $table->dropColumn('country');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('city');
            $table->dropColumn('country');
        });
    }
}
