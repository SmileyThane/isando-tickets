<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\TrackingTimesheet;

class UpdateStatusFromTimesheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (TrackingTimesheet::count() > 0) {
            TrackingTimesheet::where('status', '=', 'tracked')
                ->update(['status' => TrackingTimesheet::STATUS_TRACKED]);
            TrackingTimesheet::where('status', '=', 'pending')
                ->update(['status' => TrackingTimesheet::STATUS_PENDING]);
            TrackingTimesheet::where('status', '=', 'rejected')
                ->update(['status' => TrackingTimesheet::STATUS_REJECTED]);
            TrackingTimesheet::where('status', '=', 'archived')
                ->update(['status' => TrackingTimesheet::STATUS_ARCHIVED]);
            TrackingTimesheet::where('status', '=', 'approved')
                ->update(['status' => TrackingTimesheet::STATUS_APPROVED]);
            TrackingTimesheet::where('status', '=', 'unsubmitted')
                ->update(['status' => TrackingTimesheet::STATUS_UNSUBMITTED]);
        }

        Schema::table('tracking_timesheets', static function (Blueprint $table) {
            $table->integer('status')->default(TrackingTimesheet::STATUS_TRACKED)->change();
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
            $table->string('status', 20)->nullable(false)->default(TrackingTimesheet::STATUS_TRACKED)->change();
        });
    }
}
