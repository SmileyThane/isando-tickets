<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressTypeSeeder extends Seeder
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
                'name' => 'home',
            ]
        );
        DB::table('address_types')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'work',
            ]
        );
        DB::table('address_types')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'other',
            ]
        );
    }
}
