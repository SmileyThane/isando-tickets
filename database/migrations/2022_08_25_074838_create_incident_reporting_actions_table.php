<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentReportingActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_reporting_actions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('expired_at')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('type_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_reporting_actions');
    }
}
