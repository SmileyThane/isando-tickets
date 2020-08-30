<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('plans')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Free',
                'slug' => 'FREE',
                'description' => 'Free plan',
                'is_default' => 1,
                'is_active' => 1
            ]
        );
    }
}
