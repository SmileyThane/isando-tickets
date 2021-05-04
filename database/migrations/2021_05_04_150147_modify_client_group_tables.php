<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyClientGroupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('client_group_has_clients');
        Schema::dropIfExists('client_groups');
        Schema::dropIfExists('employee_client_groups');
        Schema::create('limitation_group_has_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_id');
            $table->string('model_type');
            $table->unsignedBigInteger('limitation_group_id');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('limitation_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->unsignedInteger('limitation_type_id');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_limitation_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_user_id');
            $table->unsignedBigInteger('limitation_group_id');
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
        Schema::dropIfExists('client_group_has_models');
        Schema::create('client_group_has_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('client_group_id');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::dropIfExists('limitation_groups');
        Schema::create('employee_client_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_user_id');
            $table->unsignedBigInteger('client_group_id');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::dropIfExists('employee_client_groups');
        Schema::create('client_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
