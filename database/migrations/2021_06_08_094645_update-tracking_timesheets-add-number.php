<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTrackingTimesheetsAddNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_timesheets', static function (Blueprint $table) {
            $table->bigInteger('number')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_timesheets', static function (Blueprint $table) {
            $table->dropColumn('number');
        });
    }
}
