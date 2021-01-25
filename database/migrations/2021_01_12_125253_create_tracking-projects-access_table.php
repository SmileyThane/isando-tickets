<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingProjectsAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_projects_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable(false);
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->double('rate', 8, 2)->nullable();
            $table->dateTimeTz('rate_from')->nullable();
            $table->integer('role')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('tracking_projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking_projects');
    }
}
