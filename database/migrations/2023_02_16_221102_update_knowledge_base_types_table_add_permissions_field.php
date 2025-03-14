<?php

use Database\Seeders\KBTypeSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
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
        Schema::table('knowledge_base_types', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('alias');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('knowledge_base_types', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
