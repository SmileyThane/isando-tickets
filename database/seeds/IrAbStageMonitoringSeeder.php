<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IrAbStageMonitoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('incident_reporting_action_board_stage_monitorings')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Stage 1',
            ]
        );

        DB::table('incident_reporting_action_board_stage_monitorings')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'Stage 2',
            ]
        );

        DB::table('incident_reporting_action_board_stage_monitorings')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'Stage 3',
            ]
        );
    }
}
