<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSequenceFieldAtTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', static function (Blueprint $table) {
            $table->unsignedInteger('sequence')->default(0);
        });

        $companies = DB::table('tickets')->distinct()->orderBy('to_entity_id')->pluck('to_entity_id');
        foreach ($companies as $companyId) {
            $dates = DB::table('tickets')->distinct()->where('to_entity_id', $companyId)->orderBy('created_at')->pluck('created_at')->toArray();

            $dates = array_unique(array_map(function ($date) {
                return date('Y-m-d', strtotime($date));
            }, $dates));

            foreach ($dates as $date) {
                $tickets = DB::table('tickets')->where('to_entity_id', $companyId)->whereDate('created_at', '=', $date)->orderBy('id')->pluck('id')->toArray();

                for ($i = 0; $i < count($tickets); $i++) {
                    DB::table('tickets')->where('id', $tickets[$i])->update(['sequence' => $i+1]);
                }
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
        Schema::table('tickets', static function (Blueprint $table) {
            $table->dropColumn('sequence');
        });
    }
}
