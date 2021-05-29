<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTicketTypesAddRelatedEntity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_types', static function (Blueprint $table) {
            $table->string('name_de')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('entity_id')->default(9);   // Inax AG
            $table->string('entity_type')->default('App\\\Company');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_types', static function (Blueprint $table) {
            $table->dropColumn('name_de');
            $table->dropColumn('icon');
            $table->dropColumn('entity_id');
            $table->dropColumn('entity_type');
        });
    }
}
