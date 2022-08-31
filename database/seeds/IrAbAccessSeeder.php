<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IrAbAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('incident_reporting_action_board_accesses')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'full',
            ]
        );

        DB::table('incident_reporting_action_board_accesses')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'limited',
            ]
        );
    }
}
