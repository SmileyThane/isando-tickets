<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //standard permissions

    public const TICKET_READ_ACCESS = 1;
    public const TICKET_WRITE_ACCESS = 2;
    public const TICKET_DELETE_ACCESS = 3;

    public const TRACKER_READ_ACCESS = 4;
    public const TRACKER_WRITE_ACCESS = 5;
    public const TRACKER_DELETE_ACCESS = 6;

    public const CLIENT_READ_ACCESS = 7;
    public const CLIENT_WRITE_ACCESS = 8;
    public const CLIENT_DELETE_ACCESS = 9;

    public const EMPLOYEE_READ_ACCESS = 10;
    public const EMPLOYEE_WRITE_ACCESS = 11;
    public const EMPLOYEE_DELETE_ACCESS = 12;

    public const COMPANY_READ_ACCESS = 13;
    public const COMPANY_WRITE_ACCESS = 14;
    public const COMPANY_DELETE_ACCESS = 15;

    public const INDIVIDUAL_READ_ACCESS = 16;
    public const INDIVIDUAL_WRITE_ACCESS = 17;
    public const INDIVIDUAL_DELETE_ACCESS = 18;

    public const PRODUCT_READ_ACCESS = 19;
    public const PRODUCT_WRITE_ACCESS = 20;
    public const PRODUCT_DELETE_ACCESS = 21;

    public const NOTIFICATION_READ_ACCESS = 22;
    public const NOTIFICATION_WRITE_ACCESS = 23;
    public const NOTIFICATION_DELETE_ACCESS = 24;

    public const IXARMA_READ_ACCESS = 25;
    public const IXARMA_WRITE_ACCESS = 26;
    public const IXARMA_DELETE_ACCESS = 27;

    public const SETTINGS_READ_ACCESS = 28;
    public const SETTINGS_WRITE_ACCESS = 29;
    public const SETTINGS_DELETE_ACCESS = 30;

    public const TEAM_READ_ACCESS = 31;
    public const TEAM_WRITE_ACCESS = 32;
    public const TEAM_DELETE_ACCESS = 33;

    public const PERMISSION_READ_ACCESS = 34;
    public const PERMISSION_WRITE_ACCESS = 35;

    public const EMPLOYEE_CLIENT_ACCESS = 36;
    public const EMPLOYEE_USER_ACCESS = 37;

    public const EMPLOYEE_TICKET_MANAGER_ACCESS = 38;
    public const EMPLOYEE_TICKET_ADMIN_ACCESS = 39;

    //custom permissions
	public const VOIP_DELETE_ACCESS = 115;
	public const VOIP_WRITE_ACCESS = 114;
	public const VOIP_READ_ACCESS = 113;
	public const VOIP_CREATE_ACCESS = 112;
	public const HOSTING_DELETE_ACCESS = 111;
	public const HOSTING_WRITE_ACCESS = 110;
	public const HOSTING_READ_ACCESS = 109;
	public const HOSTING_CREATE_ACCESS = 108;
	public const ACTIVITY_DELETE_ACCESS = 107;
	public const ACTIVITY_WRITE_ACCESS = 106;
	public const ACTIVITY_READ_ACCESS = 105;
    public const IR_CREATE_ACCESS = 104;
    public const KB_CREATE_ACCESS = 103;
    public const IR_DELETE_ACCESS = 102;
    public const IR_EDIT_ACCESS = 101;
    public const IR_VIEW_ACCESS = 100;
    public const KB_DELETE_ACCESS = 99;
    public const KB_EDIT_ACCESS = 98;
    public const KB_VIEW_ACCESS = 97;
    public const TRACKER_SETTINGS_CONTROL_REPORT = 96;
    public const SETTINGS_VIEW_COMPANY_LOGO = 95;
    public const SETTINGS_EDIT_COMPANY_LOGO = 94;
    public const TRACKER_DELETE_TEAM_TIME_ACCESS = 93;
    public const TRACKER_EDIT_TEAM_TIME_ACCESS = 92;
    public const TRACKER_REPORT_VIEW_COMPANY_TIME_ACCESS = 91;
    public const TRACKER_VIEW_COMPANY_TIME_ACCESS = 90;
    public const VIEW_LIMITATION_GROUPS_SECTION = 89;
    public const VIEW_INTERNAL_BILLING_SECTION = 88;
    public const VIEW_PRODUCT_CATEGORIES_SECTION = 87;
    public const VIEW_PRODUCT_INFO_SECTION = 86;
    public const VIEW_ROLES_OF_EMPLOYEES = 85;
    public const EDIT_ROLES_OF_EMPLOYEES = 84;
    public const EDIT_TEAM_MANAGER_FLAG = 83;
    public const CLIENT_GROUPS_DEPENDENCY = 82;
    public const TRACKER_SETTINGS_FILL_WORKING_HOURS_ACCESS = 81;
    public const TRACKER_SETTINGS_TOGGLE_TIMESHEET_ACCESS = 80;
    public const TRACKER_SETTINGS_VIEW_GENERAL_ACCESS = 79;
    public const TRACKER_SETTINGS_DELETE_SERVICES_ACCESS = 78;
    public const TRACKER_SETTINGS_EDIT_SERVICES_ACCESS = 77;
    public const TRACKER_SETTINGS_CREATE_SERVICES_ACCESS = 76;
    public const TRACKER_SETTINGS_VIEW_SERVICES_ACCESS = 75;
    public const TRACKER_REPORT_DOWNLOAD_COMPANY_TIME_ACCESS = 74;
    public const TRACKER_REPORT_DOWNLOAD_TEAM_TIME_ACCESS = 73;
    public const TRACKER_REPORT_DOWNLOAD_OWN_TIME_ACCESS = 72;
    public const TRACKER_REPORT_VIEW_REVENUE_IN_PDF_ACCESS = 71;
    public const TRACKER_REPORT_VIEW_REVENUE_PREVIEW_ACCESS = 70;
    public const TRACKER_REPORT_VIEW_REVENUE_DIAGRAM_ACCESS = 69;
    public const TRACKER_REPORT_ADD_TEAM_TIME_ACCESS = 68;
    public const TRACKER_REPORT_EDIT_TEAM_TIME_ACCESS = 67;
    public const TRACKER_REPORT_VIEW_TEAM_TIME_ACCESS = 66;
    public const TRACKER_REPORT_TOGGLE_BILLABLE_SELECT_ACCESS = 65;
    public const TRACKER_REPORT_EDIT_DELETE_OWN_TIME_2W_ACCESS = 64;
    public const TRACKER_REPORT_EDIT_DELETE_OWN_TIME_1W_ACCESS = 63;
    public const TRACKER_REPORT_EDIT_DELETE_OWN_TIME_UNLIMITED_ACCESS = 62;
    public const TRACKER_REPORT_VIEW_OWN_TIME_ACCESS = 61;
    public const TRACKER_EDIT_RATES_PROJECT_ACCESS = 60;
    public const TRACKER_VIEW_RATES_PROJECT_ACCESS = 59;
    public const TRACKER_DELETE_PROJECTS_ACCESS = 58;
    public const TRACKER_EDIT_PROJECTS_ACCESS = 57;
    public const TRACKER_VIEW_PROJECT_DETAILS_ACCESS = 56;
    public const TRACKER_VIEW_TEAM_PROJECTS_ACCESS = 55;
    public const TRACKER_VIEW_PROJECTS_ACCESS = 54;
    public const TRACKER_CREATE_PROJECTS_ACCESS = 53;
    public const TRACKER_VIEW_CALENDAR_ACCESS = 52;
    public const TRACKER_VIEW_DASHBOARD_ACCESS = 51;
    public const TRACKER_SELECT_TICKETS_ACCESS = 50;
    public const TRACKER_SELECT_PROJECTS_ACCESS = 49;
    public const TRACKER_CREATE_PROJECT_FROM_TRACKER_ACCESS = 48;
    public const TRACKER_DISABLE_ADDING_TIME_MANUALLY_ACCESS = 47;
    public const TRACKER_TOGGLE_BILLABLE_SELECT_ACCESS = 46;
    public const TRACKER_EDIT_DELETE_OWN_TIME_2W_ACCESS = 45;
    public const TRACKER_EDIT_DELETE_OWN_TIME_1W_ACCESS = 44;
    public const TRACKER_EDIT_DELETE_OWN_TIME_UNLIMITED_ACCESS = 43;
    public const TRACKER_VIEW_TEAM_TIME_ACCESS = 42;
    public const TRACKER_VIEW_OWN_TIME_ACCESS = 41;
    public const TRACKER_OWN_TIME_ACCESS = 40;
}
