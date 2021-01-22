<?php

namespace App;

class TicketNotificationType
{
    const ALL = 9;

    const NEW_ASSIGNED_TO_ME = 101;
    const NEW_ASSIGNED_TO_TEAM = 201;
    const NEW_ASSIGNED_TO_COMPANY = 301;

    const UPDATE_ASSIGNED_TO_ME = 102;
    const UPDATE_ASSIGNED_TO_TEAM = 202;
    const UPDATE_ASSIGNED_TO_COMPANY = 302;

    const CLIENT_RESPONSE_ASSIGNED_TO_ME = 103;
}
