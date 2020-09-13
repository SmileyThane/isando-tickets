<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhoneTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('phone_types')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'primary',
            ]
        );
        DB::table('phone_types')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'secondary',
            ]
        );
        DB::table('phone_types')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'other',
            ]
        );
    }
}
