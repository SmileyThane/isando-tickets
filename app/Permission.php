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
}
