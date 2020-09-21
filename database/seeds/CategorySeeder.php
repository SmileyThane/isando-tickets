<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('ticket_categories')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'client',
            ]
        );
        DB::table('ticket_categories')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'notification',
            ]
        );
        DB::table('ticket_categories')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'internal',
            ]
        );
    }
}
