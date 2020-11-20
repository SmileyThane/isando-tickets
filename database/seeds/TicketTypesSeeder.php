<?php

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
        DB::table('address_types')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'question'
            ]
        );
        DB::table('phone_types')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'issue'
            ]
        );
        DB::table('social_types')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'quote_request'
            ]
        );
    }
}
