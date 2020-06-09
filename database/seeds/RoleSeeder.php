<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'superadmin',
                'guard_name' => 'web'
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'license_owner',
                'guard_name' => 'web'
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'company_admin',
                'guard_name' => 'web'
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'company_manager',
                'guard_name' => 'web'
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 5],
            [
                'name' => 'regular_user',
                'guard_name' => 'web'
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 6],
            [
                'name' => 'company_client',
                'guard_name' => 'web'
            ]
        );
    }
}
