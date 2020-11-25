<?php

use Illuminate\Database\Seeder;

class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('social_types')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Home'
            ]
        );
        DB::table('social_types')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'Mobile'
            ]
        );
        DB::table('social_types')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'Website'
            ]
        );
    }
}
