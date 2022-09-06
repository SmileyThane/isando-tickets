<?php

use App\IncidentReportingAction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeadlineFieldsAtIncidentReportingActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_reporting_actions', static function (Blueprint $table) {
            $table->enum('deadline_time_indicator', IncidentReportingAction::DEADLINE_TIME_INDICATOR)
                ->after('expired_at')
                ->nullable();
            $table->integer('deadline_time_value')->after('expired_at')->nullable();
            $table->enum('deadline_time_parameter', IncidentReportingAction::DEADLINE_TIME_PARAMETER)
                ->after('expired_at')
                ->nullable();
            $table->dropColumn('expired_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_reporting_actions', static function (Blueprint $table) {
            $table->dropColumn('deadline_time_indicator');
            $table->dropColumn('deadline_time_value');
            $table->dropColumn('deadline_time_parameter');
            $table->dateTime('expired_at');
        });
    }
}
