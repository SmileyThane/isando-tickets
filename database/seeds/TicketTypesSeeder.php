<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TicketTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('ticket_types')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'question'
            ]
        );
        DB::table('ticket_types')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'issue'
            ]
        );
        DB::table('ticket_types')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'quote_request'
            ]
        );
        DB::table('ticket_types')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'internal'
            ]
        );
        DB::table('ticket_types')->updateOrInsert(
            ['id' => 6],
            [
                'name' => 'Idea - DEV'
            ]
        );
        DB::table('ticket_types')->updateOrInsert(
            ['id' => 7],
            [
                'name' => 'Project'
            ]
        );
        DB::table('ticket_types')->updateOrInsert(
            ['id' => 8],
            [
                'name' => 'Request'
            ]
        );
        DB::table('ticket_types')->updateOrInsert(
            ['id' => 9],
            [
                'name' => 'Offer'
            ]
        );
    }
}
