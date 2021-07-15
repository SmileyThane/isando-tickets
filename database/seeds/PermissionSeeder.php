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
        //original permissions
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
        DB::table('permissions')->updateOrInsert(
            ['id' => 28],
            [
                'name' => 'settings_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 29],
            [
                'name' => 'settings_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 30],
            [
                'name' => 'settings_delete_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 31],
            [
                'name' => 'team_read_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 32],
            [
                'name' => 'team_write_access',
                'guard_name' => 'web'
            ]
        );
        DB::table('permissions')->updateOrInsert(
            ['id' => 33],
            [
                'name' => 'team_delete_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 34],
            [
                'name' => 'permission_read_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 35],
            [
                'name' => 'permission_write_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 36],
            [
                'name' => 'employee_client_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 37],
            [
                'name' => 'employee_user_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 38],
            [
                'name' => 'employee_ticket_manager_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 39],
            [
                'name' => 'employee_ticket_admin_access',
                'guard_name' => 'web'
            ]
        );

        //custom permissions

        DB::table('permissions')->updateOrInsert(
            ['id' => 95],
            [
                'name' => 'settings_view_company_logo',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 94],
            [
                'name' => 'settings_edit_company_logo',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 93],
            [
                'name' => 'tracker_delete_team_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 92],
            [
                'name' => 'tracker_edit_team_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 91],
            [
                'name' => 'tracker_report_view_company_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 90],
            [
                'name' => 'tracker_view_company_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 89],
            [
                'name' => 'view_limitation_groups_section',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 88],
            [
                'name' => 'view_internal_billing_section',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 87],
            [
                'name' => 'view_product_categories_section',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 86],
            [
                'name' => 'view_product_info_section',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 85],
            [
                'name' => 'view_roles_of_employees',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 84],
            [
                'name' => 'edit_roles_of_employees',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 83],
            [
                'name' => 'edit_team_manager_flag',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 82],
            [
                'name' => 'client_groups_dependency',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 81],
            [
                'name' => 'tracker_settings_fill_working_hours_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 80],
            [
                'name' => 'tracker_settings_toggle_timesheet_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 79],
            [
                'name' => 'tracker_settings_view_general_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 78],
            [
                'name' => 'tracker_settings_delete_services_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 77],
            [
                'name' => 'tracker_settings_edit_services_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 76],
            [
                'name' => 'tracker_settings_create_services_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 75],
            [
                'name' => 'tracker_settings_view_services_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 74],
            [
                'name' => 'tracker_report_download_company_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 73],
            [
                'name' => 'tracker_report_download_team_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 72],
            [
                'name' => 'tracker_report_download_own_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 71],
            [
                'name' => 'tracker_report_view_revenue_in_pdf_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 70],
            [
                'name' => 'tracker_report_view_revenue_preview_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 69],
            [
                'name' => 'tracker_report_view_revenue_diagram_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 68],
            [
                'name' => 'tracker_report_add_team_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 67],
            [
                'name' => 'tracker_report_edit_team_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 66],
            [
                'name' => 'tracker_report_view_team_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 65],
            [
                'name' => 'tracker_report_toggle_billable_select_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 64],
            [
                'name' => 'tracker_report_edit_delete_own_time_2w_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 63],
            [
                'name' => 'tracker_report_edit_delete_own_time_1w_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 62],
            [
                'name' => 'tracker_report_edit_delete_own_time_unlimited_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 61],
            [
                'name' => 'tracker_report_view_own_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 60],
            [
                'name' => 'tracker_edit_rates_project_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 59],
            [
                'name' => 'tracker_view_rates_project_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 58],
            [
                'name' => 'tracker_delete_projects_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 57],
            [
                'name' => 'tracker_edit_projects_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 56],
            [
                'name' => 'tracker_view_project_details_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 55],
            [
                'name' => 'tracker_view_team_projects_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 54],
            [
                'name' => 'tracker_view_projects_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 53],
            [
                'name' => 'tracker_create_projects_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 52],
            [
                'name' => 'tracker_view_calendar_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 51],
            [
                'name' => 'tracker_view_dashboard_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 50],
            [
                'name' => 'tracker_select_tickets_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 49],
            [
                'name' => 'tracker_select_projects_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 48],
            [
                'name' => 'tracker_create_project_from_tracker_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 47],
            [
                'name' => 'tracker_disable_adding_time_manually_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 46],
            [
                'name' => 'tracker_toggle_billable_select_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 45],
            [
                'name' => 'tracker_edit_delete_own_time_2w_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 44],
            [
                'name' => 'tracker_edit_delete_own_time_1w_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 43],
            [
                'name' => 'tracker_edit_delete_own_time_unlimited_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 42],
            [
                'name' => 'tracker_view_team_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 41],
            [
                'name' => 'tracker_view_own_time_access',
                'guard_name' => 'web'
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['id' => 40],
            [
                'name' => 'tracker_track_own_time_access',
                'guard_name' => 'web'
            ]
        );
    }
}
