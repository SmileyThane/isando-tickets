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
        Schema::table('tickets', static function (Blueprint $table) {
            $table->string('from_entity_name', 1024)->nullable();
            $table->string('contact_full_name', 1024)->nullable();
            $table->string('assigned_person_full_name', 1024)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', static function (Blueprint $table) {
            $table->dropColumn('from_entity_name');
            $table->dropColumn('contact_full_name');
            $table->dropColumn('assigned_person_full_name');
        });
    }
};
