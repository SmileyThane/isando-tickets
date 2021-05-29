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
    public function run(): void
    {
        DB::table('roles')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'superadmin',
                'guard_name' => 'web',
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'company_admin',
                'guard_name' => 'web'
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'ticketing_admin',
                'guard_name' => 'web'
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'ticketing_manager',
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
        DB::table('roles')->updateOrInsert(
            ['id' => 7],
            [
                'name' => 'company_admin_tracking',
                'guard_name' => 'web'
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 8],
            [
                'name' => 'team_manager_tracking',
                'guard_name' => 'web'
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 9],
            [
                'name' => 'team_member_tracking',
                'guard_name' => 'web'
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 10],
            [
                'name' => 'contractor',
                'guard_name' => 'web',
                'is_public' => false
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 11],
            [
                'name' => 'SC',
                'guard_name' => 'web',
                'is_public' => false
            ]
        );
        DB::table('roles')->updateOrInsert(
            ['id' => 12],
            [
                'name' => 'test',
                'guard_name' => 'web',
                'is_public' => false
            ]
        );
    }
}
