<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IrAbStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('incident_reporting_action_board_states')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'State 1',
            ]
        );

        DB::table('incident_reporting_action_board_states')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'State 2',
            ]
        );

        DB::table('incident_reporting_action_board_states')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'State 3',
            ]
        );
    }
}
