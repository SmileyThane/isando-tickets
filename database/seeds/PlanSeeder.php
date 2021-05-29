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
                'is_active' => 1,
                'max_employees' => 50,
                'max_clients' => 100,
                'max_products' => 10,
                'max_teams' => 10

            ]
        );
        DB::table('plan_prices')->updateOrInsert(
            ['id' => 1],
            [
                'plan_id' => 1,
            ]
        );
        DB::table('plans')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'Easy',
                'slug' => 'EASY',
                'description' => 'Plan with small privileges',
                'is_default' => 0,
                'is_active' => 1,
                'max_employees' => 2,
                'max_clients' => 2,
                'max_products' => 1,
                'max_teams' => 1
            ]
        );
        DB::table('plan_prices')->updateOrInsert(
            ['id' => 2],
            [
                'plan_id' => 2,
                'currency_id' => 1,
                'price' => 5,
            ]
        );
        DB::table('plan_prices')->updateOrInsert(
            ['id' => 3],
            [
                'plan_id' => 2,
                'currency_id' => 2,
                'price' => 3,
            ]
        );
        DB::table('plans')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'Medium',
                'slug' => 'MID',
                'description' => 'Plan with medium privileges',
                'is_default' => 0,
                'is_active' => 1,
                'max_employees' => 5,
                'max_clients' => 10,
                'max_products' => 2,
                'max_teams' => 4
            ]
        );
        DB::table('plan_prices')->updateOrInsert(
            ['id' => 4],
            [
                'plan_id' => 3,
                'currency_id' => 1,
                'price' => 12,
            ]
        );
        DB::table('plan_prices')->updateOrInsert(
            ['id' => 5],
            [
                'plan_id' => 3,
                'currency_id' => 2,
                'price' => 10,
            ]
        );
        DB::table('plans')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'Hard',
                'slug' => 'MAX',
                'description' => 'Plan with maximum privileges',
                'is_default' => 0,
                'is_active' => 1,
                'max_employees' => 1000,
                'max_clients' => 1000,
                'max_products' => 100,
                'max_teams' => 100
            ]
        );
        DB::table('plan_prices')->updateOrInsert(
            ['id' => 6],
            [
                'plan_id' => 4,
                'currency_id' => 1,
                'price' => 50,
            ]
        );
        DB::table('plan_prices')->updateOrInsert(
            ['id' => 7],
            [
                'plan_id' => 4,
                'currency_id' => 2,
                'price' => 45,
            ]
        );
    }
}
