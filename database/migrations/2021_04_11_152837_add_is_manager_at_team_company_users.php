<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsManagerAtTeamCompanyUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_company_users', static function (Blueprint $table) {
            $table->boolean('is_manager')->default(false)->after('company_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_company_users', static function (Blueprint $table) {
            $table->dropColumn('is_manager');
        });
    }
}
