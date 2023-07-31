<?php

namespace Database\Seeders;

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
                'color' => '#B90E0A'
            ]
        );
        DB::table('ticket_priorities')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'moderate',
                'color' => '#FEBE00'
            ]
        );
        DB::table('ticket_priorities')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'low',
                'color' => '808080'
            ]
        );


    }
}
