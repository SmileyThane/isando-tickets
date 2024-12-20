<?php

use App\Tracking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTrackingStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Tracking::count() > 0) {
            Tracking::query()->update(['status' => Tracking::$STATUS_STOPPED]);
        }

        Schema::table('tracking', static function (Blueprint $table) {
            $table->integer('status')->default(Tracking::$STATUS_STOPPED)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking', static function (Blueprint $table) {
            $table->string('status', 20)->nullable(false)->default('stopped')->change();
        });
    }
}
