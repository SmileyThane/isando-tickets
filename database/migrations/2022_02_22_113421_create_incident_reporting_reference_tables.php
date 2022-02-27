<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentReportingReferenceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_reporting_action_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->unsignedTinyInteger('position')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('incident_reporting_event_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->unsignedTinyInteger('position')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('incident_reporting_focus_priorities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->unsignedTinyInteger('position')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('incident_reporting_impact_potentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->unsignedTinyInteger('position')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('incident_reporting_process_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->unsignedTinyInteger('position')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('incident_reporting_resource_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->unsignedTinyInteger('position')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('incident_reporting_stakeholder_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->unsignedTinyInteger('position')->default(1);
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
        Schema::dropIfExists('incident_reporting_action_types');  
        Schema::dropIfExists('incident_reporting_event_types');  
        Schema::dropIfExists('incident_reporting_focus_priorities');  
        Schema::dropIfExists('incident_reporting_impact_potentials');  
        Schema::dropIfExists('incident_reporting_resource_types');  
        Schema::dropIfExists('incident_reporting_stakeholder_types');  
    }
}
