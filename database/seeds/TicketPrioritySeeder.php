<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('ticket_priorities')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'urgent',
            ]
        );
        DB::table('ticket_priorities')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'moderate',
            ]
        );
        DB::table('ticket_priorities')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'low',
            ]
        );


    }
}
