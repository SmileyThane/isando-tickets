<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTrackingAddMorphRealtions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking', static function (Blueprint $table) {
//            $table->dropForeign('tracking_tracking_projects_id_fk');
//            $table->dropIndex('tracking_tracking_projects_id_fk');
            $table->renameColumn('project_id', 'entity_id')->change();
        });
        Schema::table('tracking', static function (Blueprint $table) {
            $table->string('entity_type')
                ->nullable()
                ->after('entity_id');
        });
        \App\Tracking::whereNotNull('entity_id')->update(['entity_type' => 'App\\TrackingProject']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking', static function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable();
            $table->dropColumn('entity_type');
        });
    }
}
