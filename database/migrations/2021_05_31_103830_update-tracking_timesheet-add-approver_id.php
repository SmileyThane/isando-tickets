<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTrackingTimesheetAddApproverId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_timesheets', static function (Blueprint $table) {
            $table->string('status', 255)->default('tracked')->change();
            $table->bigInteger('approver_id')->nullable()->after('status');
            $table->text('note')->nullable()->after('approver_id');
            $table->date('submitted_on')->nullable()->after('approver_id');
            $table->dateTime('notification_date')->nullable()->after('submitted_on');
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
            $table->dropColumn('approver_id');
            $table->dropColumn('note');
            $table->dropColumn('submitted_on');
            $table->dropColumn('notification_date');
        });
    }
}
