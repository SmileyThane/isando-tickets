<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingTimesheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_timesheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable(false);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('team_id')->nullable()->constrained('teams');
            $table->foreignId('company_id')->nullable()->constrained('companies');
            $table->boolean('is_manually')->default(false);
            $table->date('from')->nullable(false);
            $table->date('to')->nullable(false);
            $table->enum('status', ['tracked', 'pending', 'unsubmitted', 'archived'])->default('tracked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking_timesheets');
    }
}
