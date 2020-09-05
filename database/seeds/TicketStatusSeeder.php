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
    public function run(): void
    {
        DB::table('ticket_statuses')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'new',
                'color' => 'blue'
            ]
        );
        DB::table('ticket_statuses')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'open',
                'color' => 'amber'
            ]
        );
        DB::table('ticket_statuses')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'responded',
                'color' => 'amber'
            ]
        );
        DB::table('ticket_statuses')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'waiting_for_reply',
                'color' => 'green'
            ]
        );
        DB::table('ticket_statuses')->updateOrInsert(
            ['id' => 5],
            [
                'name' => 'closed',
                'color' => 'grey'
            ]
        );
    }
}
