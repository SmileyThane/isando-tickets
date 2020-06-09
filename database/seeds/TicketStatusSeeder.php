<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ticket_statuses')->insert([
            0 =>
                [
                    'id' => 1,
                    'name' => 'New',
                ],
            1 =>
                [
                    'id' => 2,
                    'name' => 'Open (Urgent)',
                ],
            2 =>
                [
                    'id' => 3,
                    'name' => 'Open (Moderate)',
                ],
            3 =>
                [
                    'id' => 4,
                    'name' => 'Waiting for reply',
                ],
            4 =>
                [
                    'id' => 5,
                    'name' => 'Closed',
                ],
        ]);
    }
}
