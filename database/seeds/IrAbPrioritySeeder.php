<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IrAbPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('incident_reporting_action_board_priorities')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'high',
            ]
        );

        DB::table('incident_reporting_action_board_priorities')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'medium',
            ]
        );

        DB::table('incident_reporting_action_board_priorities')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'low',
            ]
        );
    }
}
