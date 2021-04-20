<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdAndCompanyIdAtTrackingProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_projects', static function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('client_id');
            $table->unsignedBigInteger('company_id')->nullable()->after('client_id');
        });

        foreach (\App\TrackingProject::all() as $trackingProject) {
            $tracking = $trackingProject->Trackers()->first();
            if ($tracking) {
                $trackingProject->company_id = $tracking->company_id;
                $trackingProject->team_id = $tracking->team_id;
                $trackingProject->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_projects', static function (Blueprint $table) {
            $table->dropColumn('team_id');
            $table->dropColumn('company_id');
        });
    }
}
