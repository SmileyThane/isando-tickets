<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRateFieldAtTackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking', static function (Blueprint $table) {
            $table->double('rate', 8, 2)->nullable()->after('billed');
        });

        $trackings = \App\Tracking::where('entity_type', '=', \App\TrackingProject::class)->get();
        foreach($trackings as $tracking) {
            if ($tracking->entity_id) {
                $tracking->rate = $tracking->entity->rate;
                $tracking->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking', static function (Blueprint $table) {
            $table->dropColumn('rate');
        });
    }
}
