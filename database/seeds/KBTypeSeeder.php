<?php

use Illuminate\Database\Seeder;

class KBTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('knowledge_base_types')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Knowledge base',
                'alias' => 'knowledge_base',
            ]
        );
        DB::table('knowledge_base_types')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'Incident management',
                'alias' => 'incident_management',
            ]
        );
    }
}
