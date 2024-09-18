<?php


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExternalSourceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('external_source_types')->updateOrInsert(
            ['name' => 'VoIP'],
            ['description' => 'Voice over IP services']
        );

        DB::table('external_source_types')->updateOrInsert(
            ['name' => 'Hosting'],
            ['description' => 'Web hosting services']
        );
    }
}
