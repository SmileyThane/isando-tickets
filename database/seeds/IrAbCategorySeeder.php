<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IrAbCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('incident_reporting_action_board_categories')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Category 1',
            ]
        );

        DB::table('incident_reporting_action_board_categories')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'Category 2',
            ]
        );

        DB::table('incident_reporting_action_board_categories')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'Category 3',
            ]
        );

        DB::table('incident_reporting_action_board_categories')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'Category 4',
            ]
        );

        DB::table('incident_reporting_action_board_categories')->updateOrInsert(
            ['id' => 5],
            [
                'name' => 'Category 5',
            ]
        );
    }
}
