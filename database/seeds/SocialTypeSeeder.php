<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialTypeSeeder extends Seeder
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
                'name' => 'website',
            ]
        );
        DB::table('social_types')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'linkedin',
            ]
        );
        DB::table('social_types')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'facebook',
            ]
        );
        DB::table('social_types')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'contact_email',
            ]
        );
    }
}
