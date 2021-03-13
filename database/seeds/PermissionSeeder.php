<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'ticket_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'ticket_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'ticket_delete_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'tracker_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 5],
            [
                'name' => 'tracker_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 6],
            [
                'name' => 'tracker_delete_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 7],
            [
                'name' => 'client_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 8],
            [
                'name' => 'client_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 9],
            [
                'name' => 'client_delete_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 10],
            [
                'name' => 'employee_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 11],
            [
                'name' => 'employee_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 12],
            [
                'name' => 'employee_delete_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 13],
            [
                'name' => 'company_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 14],
            [
                'name' => 'company_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 15],
            [
                'name' => 'company_delete_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 16],
            [
                'name' => 'individual_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 17],
            [
                'name' => 'individual_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 18],
            [
                'name' => 'individual_delete_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 19],
            [
                'name' => 'product_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 20],
            [
                'name' => 'product_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 21],
            [
                'name' => 'product_delete_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 22],
            [
                'name' => 'notification_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 23],
            [
                'name' => 'notification_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 24],
            [
                'name' => 'notification_delete_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 25],
            [
                'name' => 'ixarma_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 26],
            [
                'name' => 'ixarma_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 27],
            [
                'name' => 'ixarma_delete_access',
                'guard_name' => 'web'
            ]
        );
    }
}
