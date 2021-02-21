<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionFieldsAtClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', static function (Blueprint $table) {
            $table->boolean('has_ticketing')->default(0);
            $table->boolean('has_tracker')->default(0);
            $table->boolean('has_ixarma')->default(0);
            $table->boolean('has_ixarma_app')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', static function (Blueprint $table) {
            $table->dropColumn('has_ticketing');
            $table->dropColumn('has_tracker');
            $table->dropColumn('has_ixarma');
            $table->dropColumn('has_ixarma_app');

        });
    }
}
