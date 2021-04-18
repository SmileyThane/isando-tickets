<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingTimesheetTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_timesheet_time', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timesheet_id')->constrained('tracking_timesheets');
            $table->enum('type', ['work', 'lunch', 'absence'])->nullable(false);
            $table->date('date')->nullable(false);
            $table->time('time')->nullable(false)->default('00:00:00');
            $table->string('description')->nullable();
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
        Schema::dropIfExists('tracking_timesheet_times');
    }
}
