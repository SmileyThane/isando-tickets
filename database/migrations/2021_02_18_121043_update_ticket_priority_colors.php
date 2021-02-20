<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateTicketPriorityColors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('ticket_priorities')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'urgent',
                'color' => 'pink',
            ]
        );
        DB::table('ticket_priorities')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'moderate',
                'color' => 'amber',
            ]
        );
        DB::table('ticket_priorities')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'low',
                'color' => 'grey',
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
