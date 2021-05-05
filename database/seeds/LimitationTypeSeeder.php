<?php

use App\Client;
use App\Product;
use Illuminate\Database\Seeder;

class LimitationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('limitation_types')->updateOrInsert(
            ['id' => 1],
            [
                'short_code' => 'customer',
                'model' => Client::class,
            ]
        );
        DB::table('limitation_types')->updateOrInsert(
            ['id' => 2],
            [
                'short_code' => 'product',
                'model' => Product::class
            ]
        );
    }
}
