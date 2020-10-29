<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeSettingsUniversal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_settings', static function (Blueprint $table) {
            //$table->dropForeign(['company_id']);
            $table->string('entity_type')->nullable();
            $table->renameColumn('company_id', 'entity_id');
        });

        DB::table('company_settings')->update(['entity_type' => \App\Company::class]);
        Schema::rename('company_settings', 'settings');

        Schema::table('settings', static function (Blueprint $table) {
            $table->string('entity_type')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->where('entity_type', '<>', \App\Company::class)->delete();

        Schema::rename('settings', 'company_settings');
        Schema::table('company_settings', static function (Blueprint $table) {
            $table->dropColumn('entity_type');
            $table->renameColumn('entity_id', 'company_id');
            $table->foreign(['company_id']);
        });
    }
}
