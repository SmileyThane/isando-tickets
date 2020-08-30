<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('currencies')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'US Dollar',
                'slug' => 'USD',
                'symbol' => '£'
            ]
        );
        DB::table('currencies')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'Euro',
                'slug' => 'EUR',
                'symbol' => '£'
            ]
        );
    }
}
