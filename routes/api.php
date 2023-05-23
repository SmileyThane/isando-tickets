<?php

use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\ClientGroupController;
use App\Http\Controllers\API\IncidentReportingController;
use App\Http\Controllers\API\InternalBillingController;
use App\Http\Controllers\API\LimitationGroupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'API\AuthController@register');
Route::post('login', 'API\AuthController@login');
Route::post('reset_password', 'API\AuthController@resetPassword');
Route::get('plans/{groupedBy?}', 'API\AuthController@plans');
Route::get('/currencies', 'API\CurrencyController@get');
Route::get('time_zones', 'HomeController@getTimeZones');
Route::get('/mail/receive/{type?}', 'HomeController@receiveMail')->name('receiveEmail');
Route::get('version', 'API\AuthController@getAppVersion');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('auth/check', 'Controller@checkAuth');
    Route::get('logout', 'API\AuthController@logout');
    Route::get('roles', 'API\RoleController@roles');
    Route::get('roles/full', 'API\RoleController@getRolesWithPermissions');
    Route::put('roles/full', 'API\RoleController@mergeRolesWithPermissions');
    Route::get('permissions', 'API\RoleController@permissions');


    Route::group(['middleware' => 'setLanguage'], function () {
        //user management
        Route::get('user', 'API\UserController@find');
        Route::get('user/find/{id}', 'API\UserController@find');
        Route::post('user', 'API\UserController@update');
        Route::post('user/ixarma/{id}', 'API\UserController@updateIxarmaLink');
        Route::post('user/invite', 'API\UserController@sendInvite');
        Route::post('user/is_active', 'API\UserController@changeIsActive');
        Route::get('user/roles/id', 'API\UserController@authorizedRoleIds');
        Route::patch('user/roles', 'API\UserController@updateRoles');
        Route::get('user/permissions/id', 'API\UserController@authorizedPermissionIds');
        Route::get('user/settings', 'API\UserController@getSettings');
        Route::post('user/settings', 'API\UserController@updateSettings');
        Route::post('user/notifications', 'API\UserController@setNotifications');
        Route::post('user/avatar', 'API\UserController@updateAvatar');
        Route::delete('user/delete/{id}', 'API\UserController@delete');
        Route::post('user/restore', 'API\UserController@restoreDeleted');

        //company management
        Route::get('company/{id?}', 'API\CompanyController@find');
        Route::post('company/{id}', 'API\CompanyController@update');
        Route::post('company/product', 'API\CompanyController@attachProduct');
        Route::get('company/{id?}/product_categories/tree', 'API\CompanyController@getProductCategoriesTree');
        Route::get('company/{id?}/product_categories/flat', 'API\CompanyController@getProductCategoriesFlat');
        Route::post('company/{id}/product_category', 'API\CompanyController@attachProductCategory');
        Route::delete('product_category/{id}', 'API\CompanyController@detachProductCategory');
        Route::post('main_company/settings', 'API\CompanyController@updateSettings');
        Route::post('company/{id}/settings', 'API\CompanyController@updateSettings');
        Route::post('company/{id}/logo', 'API\CompanyController@updateLogo');
        Route::get('main_company/name', 'API\CompanyController@mainCompanyName');
        Route::get('main_company/logo', 'API\CompanyController@mainCompanyLogo');
        Route::post('main_company/logo', 'API\CompanyController@updateLogo');
        Route::get('main_company/settings', 'API\CompanyController@getSettings');
        Route::get('main_company/product_categories/tree', 'API\CompanyController@getProductCategoriesTree');
        Route::get('main_company/product_categories/flat', 'API\CompanyController@getProductCategoriesFlat');
        Route::get('main_company/license', 'API\CompanyController@mainCompanyLicense');

        //employee management
        Route::get('employee', 'API\CompanyController@getIndividuals');
        Route::post('company/{id}/employee', 'API\CompanyController@invite');
        Route::delete('company/employee/{id}', 'API\CompanyController@removeEmployee');

        //phone management
        Route::post('phone', 'API\PhoneController@add');
        Route::patch('phone/{id}', 'API\PhoneController@edit');
        Route::delete('phone/{id}', 'API\PhoneController@delete');
        Route::get('phone_types', 'API\PhoneController@getTypes');
        Route::post('phone_type', 'API\PhoneController@addType');
        Route::patch('phone_type/{id}', 'API\PhoneController@updateType');
        Route::delete('phone_type/{id}', 'API\PhoneController@deleteType');

        //social management
        Route::get('social_types', 'API\SocialController@getTypes');
        Route::post('social_type', 'API\SocialController@addType');
        Route::patch('social_type/{id}', 'API\SocialController@updateType');
        Route::delete('social_type/{id}', 'API\SocialController@deleteType');
        Route::post('social', 'API\SocialController@add');
        Route::patch('social/{id}', 'API\SocialController@edit');
        Route::delete('social/{id}', 'API\SocialController@delete');

        //address management
        Route::get('address_types', 'API\AddressController@getTypes');
        Route::post('address_type', 'API\AddressController@addType');
        Route::patch('address_type/{id}', 'API\AddressController@updateType');
        Route::delete('address_type/{id}', 'API\AddressController@deleteType');
        Route::post('address', 'API\AddressController@add');
        Route::patch('address/{id}', 'API\AddressController@edit');
        Route::delete('address/{id}', 'API\AddressController@delete');

        //email management
        Route::get('email_types', 'API\EmailController@getTypes');
        Route::post('email_type', 'API\EmailController@addType');
        Route::patch('email_type/{id}', 'API\EmailController@updateType');
        Route::delete('email_type/{id}', 'API\EmailController@deleteType');
        Route::post('email', 'API\EmailController@add');
        Route::patch('email/{id}', 'API\EmailController@edit');
        Route::delete('email/{id}', 'API\EmailController@delete');

        //client management
        Route::get('client', 'API\ClientController@clients');
        Route::get('supplier', 'API\ClientController@suppliers');
        Route::post('client', 'API\ClientController@create');
        Route::get('client/{id}', 'API\ClientController@find');
        Route::get('client/{id}/related', 'API\ClientController@relatedClients');
        Route::patch('client/{id}', 'API\ClientController@update');
        Route::delete('client/{id}', 'API\ClientController@delete');
        Route::post('client/employee', 'API\ClientController@attach');
        Route::delete('client/employee/{id}', 'API\ClientController@detach');
        Route::get('all', 'API\ClientController@all');
        Route::post('client/is_active', 'API\ClientController@changeIsActive');
        Route::get('recipients', 'API\ClientController@recipientsTree');
        Route::post('client/{id}/logo', 'API\ClientController@updateLogo');

        //team management
        Route::get('team', 'API\TeamController@get');
        Route::post('team', 'API\TeamController@create');
        Route::get('team/{id}', 'API\TeamController@find');
        Route::patch('team/{id}', 'API\TeamController@update');
        Route::delete('team/{id}', 'API\TeamController@delete');
        Route::post('team/employee', 'API\TeamController@attach');
        Route::post('team/employee/manager', 'API\TeamController@toggleAsManager');
        Route::delete('team/employee/{id}', 'API\TeamController@detach');

        //product management
        Route::get('product', 'API\ProductController@get');
        Route::post('product', 'API\ProductController@create');
        Route::get('product/{id}', 'API\ProductController@find');
        Route::post('product/{id}', 'API\ProductController@update');
        Route::delete('product/{id}', 'API\ProductController@delete');
        Route::post('product/employee', 'API\ProductController@attachEmployee');
        Route::delete('product/employee/{id}', 'API\ProductController@detachEmployee');
        Route::post('product/client', 'API\ProductController@attachClient');
        Route::delete('product/client/{id}', 'API\ProductController@detachClient');

        //ticket management
        Route::get('ticket', 'API\TicketController@get');
        Route::post('ticket', 'API\TicketController@create');
        Route::get('ticket/{id}', 'API\TicketController@find');
        Route::patch('ticket/{id}', 'API\TicketController@update');
        Route::post('ticket/{id}', 'API\TicketController@updateDescription');
        Route::delete('ticket/{id}', 'API\TicketController@delete');
        Route::post('ticket/{id}/team', 'API\TicketController@attachTeam');
        Route::post('ticket/{id}/employee', 'API\TicketController@attachEmployee');
        Route::post('ticket/{id}/contact', 'API\TicketController@attachContact');
        Route::post('ticket/{id}/answer', 'API\TicketController@addAnswer');
        Route::post('ticket/{id}/answer/{answer_id}', 'API\TicketController@editAnswer');
        Route::post('ticket/{id}/notice', 'API\TicketController@addNotice');
        Route::post('ticket/{id}/notice/{notice_id}', 'API\TicketController@editNotice');
        Route::get('ticket_priorities', 'API\TicketController@priorities');
        Route::get('ticket_types', 'API\TicketController@getTypes');
        Route::get('ticket_types/all', 'API\TicketController@getAllTypesInCompanyContext');
        Route::post('ticket_type', 'API\TicketController@addType');
        Route::patch('ticket_type/{id}', 'API\TicketController@updateType');
        Route::delete('ticket_type/{id}', 'API\TicketController@deleteType');
        Route::get('ticket_categories', 'API\TicketController@categories');
        Route::post('merge/ticket', 'API\TicketController@addMerge');
        Route::delete('merge/ticket/{id}', 'API\TicketController@removeMerge');
        Route::post('link/ticket', 'API\TicketController@addLink');
        Route::post('spam/ticket', 'API\TicketController@markAsSpam');

        //custom ticket filter
        Route::get('ticket_filters', 'API\TicketController@getFilters');
        Route::get('ticket_filter_parameters', 'API\TicketController@getFilterParameters');
        Route::post('ticket_query', 'API\TicketController@addFilter');
        Route::delete('ticket_filters/{filterId}', 'API\TicketController@removeFilter');

        //notifications management
        Route::get('notification_types', 'API\NotificationController@getTypes');
        Route::post('notification_type', 'API\NotificationController@addType');
        Route::patch('notification_type/{id}', 'API\NotificationController@updateType');
        Route::delete('notification_type/{id}', 'API\NotificationController@deleteType');
        Route::get('notifications', 'API\NotificationController@get');
        Route::get('notification/{id}', 'API\NotificationController@find');
        Route::post('notification', 'API\NotificationController@add');
        Route::patch('notification/{id}', 'API\NotificationController@edit');
        Route::delete('notification/{id}', 'API\NotificationController@delete');
        Route::post('notification/send', 'API\NotificationController@send');
        Route::get('notifications/history', 'API\NotificationController@history');
        Route::get('notification/history/{id}', 'API\NotificationController@historyDetails');

        //email signatures management
        Route::get('email_signatures', 'API\EmailSignatureController@get');
        Route::post('email_signature', 'API\EmailSignatureController@add');
        Route::patch('email_signature/{id}', 'API\EmailSignatureController@update');
        Route::delete('email_signature/{id}', 'API\EmailSignatureController@delete');

        //Tracking projects
        Route::prefix('ttmanaging')
            ->namespace('API\Tracking')->group(function () {
                //Tracking projects
                Route::get('/projects', 'ProjectController@get');
                Route::get('/projects/{id}', 'ProjectController@find');
                Route::post('/projects', 'ProjectController@create');
                Route::patch('/projects/{id}', 'ProjectController@update');
                Route::delete('/projects/{id}', 'ProjectController@delete');
                Route::patch('/projects/{id}/favorite', 'ProjectController@toggleFavorite');
                Route::patch('/projects/{id}/archive', 'ProjectController@toggleArchive');

                //Additional tracking routes
                Route::get('/clients', 'BaseController@getClientList');
                Route::get('/products', 'BaseController@getProductList');
                Route::get('/coworkers', 'BaseController@getCoworkers');
                Route::get('/managed_teams', 'BaseController@getManagedTeams');
                Route::get('/team_managers', 'BaseController@getManagersOfTeams');
                Route::get('/tickets', 'BaseController@getTickets');

                //Tracker
                Route::get('/ttmanager', 'TrackingController@get');
                Route::post('/ttmanager', 'TrackingController@create');
                Route::patch('/ttmanager/{ttmanaging}', 'TrackingController@update');
                Route::post('/ttmanager/{ttmanaging}/duplicate', 'TrackingController@duplicate');
                Route::delete('/ttmanager/{ttmanaging}', 'TrackingController@delete');

                // Reports
                Route::post('/reports', 'ReportController@create');
                Route::delete('/reports/{id}', 'ReportController@delete');
                Route::get('/reports/{id}', 'ReportController@find');
                Route::get('/reports', 'ReportController@get');
                Route::post('/reports/generate', 'ReportController@generate');

                // Settings
                Route::get('/settings', 'SettingsController@get');
                Route::patch('/settings', 'SettingsController@update');
                Route::post('/settings/report/reconciliation', 'SettingsController@getReconciliationReport');
                Route::post('/settings/report/{source}', 'SettingsController@genReport');

                // Timesheet
                Route::get('/timesheet', 'TimesheetController@get');
                Route::post('/timesheet/export', 'TimesheetController@exportPdf');
                Route::get('/timesheet/status', 'TimesheetController@getAllGroupedByStatus');
                Route::post('/timesheet/copy_last_week', 'TimesheetController@copyPreviousWeek');
                Route::get('/timesheet/templates', 'TimesheetController@getUserTemplates');
                Route::post('/timesheet/templates', 'TimesheetController@saveAsTemplate');
                Route::post('/timesheet/templates/{template_id}', 'TimesheetController@loadTemplate');
                Route::delete('/timesheet/templates/{template_id}', 'TimesheetController@removeTemplate');
                Route::post('/timesheet', 'TimesheetController@create');
                Route::get('/timesheet/approval', 'TimesheetController@getCountTimesheetForApproval');
                Route::patch('/timesheet/ordering', 'TimesheetController@saveOrdering');
                Route::get('/timesheet/{id}', 'TimesheetController@find');
                Route::patch('/timesheet/submit', 'TimesheetController@submit');
                Route::post('/timesheet/remind', 'TimesheetController@remind');
                Route::patch('/timesheet/{id}', 'TimesheetController@update');
                Route::delete('/timesheet/{id}', 'TimesheetController@delete');

                // Dashboard
                Route::get('/dashboard', 'DashboardController@index');
            });

        // Currencies
        Route::post('/currencies', 'API\CurrencyController@create');
        Route::patch('/currencies/{currency}', 'API\CurrencyController@update');
        Route::delete('/currencies/{currency}', 'API\CurrencyController@delete');

        // Tags
        Route::get('/tags', 'API\TagController@get');
        Route::post('/tags', 'API\TagController@create');
        Route::patch('/tags/{tag}', 'API\TagController@update');
        Route::patch('/tags/{tag}/translate', 'API\TagController@createOrUpdateTranslation');
        Route::delete('/tags/{tag}', 'API\TagController@delete');

        // Services
        Route::get('/services', 'API\ServiceController@get');
        Route::post('/services', 'API\ServiceController@create');
        Route::patch('/services/{service}', 'API\ServiceController@update');
        Route::delete('/services/{service}', 'API\ServiceController@delete');
    });

    //language management
    Route::get('lang', 'API\LanguageController@getLanguages');
    Route::get('lang/map/{id?}', 'API\LanguageController@find');
    Route::get('lang/all', 'API\LanguageController@getAllLanguages');
    Route::get('lang/company/{company_id?}', 'API\LanguageController@getCompanyLanguages');
    Route::post('lang/company', 'API\LanguageController@addCompanyLanguage');
    Route::delete('lang/{id}/company/{company_id?}', 'API\LanguageController@deleteCompanyLanguage');

    // country management
    Route::get('countries', 'API\CountryController@getCountries');
    Route::get('countries/all', 'API\CountryController@getAllCountries');
    Route::get('countries/company/{company_id?}', 'API\CountryController@getCompanyCountries');
    Route::post('country/company', 'API\CountryController@addCompanyCountry');
    Route::delete('country/{id}/company/{company_id?}', 'API\CountryController@deleteCompanyCountry');

    //custom license
    Route::get('custom_license', 'API\CustomLicenseController@index');
    Route::get('custom_license/{id}/link', 'API\CustomLicenseController@create');
    Route::get('custom_license/{id}', 'API\CustomLicenseController@find');
    Route::put('custom_license/{id}', 'API\CustomLicenseController@update');
    Route::put('custom_license/{id}/limits', 'API\CustomLicenseController@updateLimits');
    Route::get('custom_license/{id}/history', 'API\CustomLicenseController@history');
    Route::get('custom_license/{id}/users', 'API\CustomLicenseController@users');
    Route::get('custom_license_unassigned', 'API\CustomLicenseController@unassignedIxarmaUsersList');
    Route::post('custom_license_unassigned/assign', 'API\CustomLicenseController@assignToIxarmaCompany');
    Route::get('custom_license/{id}/user/{remoteUserId}/{idLicensed}', 'API\CustomLicenseController@manageUser');
    Route::post('custom_license_user/unassign', 'API\CustomLicenseController@unassignFromIxarmaCompany');
    Route::put('custom_license_user/{remoteUserId}/trial', 'API\CustomLicenseController@setUserTrial');

    // knowledge base
    Route::get('kb/types', 'API\KbController@listTypes');
    Route::get('kb/importance', 'API\KbController@importanceList');
    Route::get('kb/categories', 'API\KbController@listCategories')->middleware('kb:view');
    Route::get('kb/categories/tree', 'API\KbController@categoriesTree')->middleware('kb:view');
    Route::post('kb/category', 'API\KbController@addCategory')->middleware('kb:create');
    Route::put('kb/category/{id}', 'API\KbController@editCategory')->middleware('kb:edit');
    Route::delete('kb/category/{id}', 'API\KbController@deleteCategory')->middleware('kb:delete');
    Route::get('kb/articles', 'API\KbController@listArticles')->middleware('kb:view');
    Route::get('kb/articles/all', 'API\KbController@allArticles')->middleware('kb:view');
    Route::get('kb/article/{id}', 'API\KbController@getArticle')->middleware('kb:view');
    Route::post('kb/article', 'API\KbController@addArticle')->middleware('kb:create');
    Route::put('kb/article/{id}', 'API\KbController@editArticle')->middleware('kb:edit');
    Route::delete('kb/article/{id}', 'API\KbController@deleteArticle')->middleware('kb:delete');
    Route::put('kb/{knowledgeBaseType}', 'API\KbController@update');
    Route::post('kb', 'API\KbController@create');
    Route::delete('kb/{knowledgeBaseType}', 'API\KbController@delete');


    // incident reporting
    Route::get('ir/action_types', 'API\IncidentReportingController@listActionTypes');
    Route::post('ir/action_type', 'API\IncidentReportingController@addActionType');
    Route::put('ir/action_type/{id}', 'API\IncidentReportingController@editActionType');
    Route::delete('ir/action_type/{id}', 'API\IncidentReportingController@deleteActionType');

    Route::get('ir/action_board_statuses', 'API\IncidentReportingController@listActionBoardStatuses');
    Route::post('ir/action_board_status', 'API\IncidentReportingController@addActionBoardStatus');
    Route::put('ir/action_board_status/{id}', 'API\IncidentReportingController@editActionBoardStatus');
    Route::delete('ir/action_board_status/{id}', 'API\IncidentReportingController@deleteActionBoardStatus');

    Route::get('ir/event_types', 'API\IncidentReportingController@listEventTypes');
    Route::post('ir/event_type', 'API\IncidentReportingController@addEventType');
    Route::put('ir/event_type/{id}', 'API\IncidentReportingController@editEventType');
    Route::delete('ir/event_type/{id}', 'API\IncidentReportingController@deleteEventType');
    Route::get('ir/focus_priorities', 'API\IncidentReportingController@listFocusPriorities');
    Route::post('ir/focus_priority', 'API\IncidentReportingController@addFocusPriority');
    Route::put('ir/focus_priority/{id}', 'API\IncidentReportingController@editFocusPriority');
    Route::delete('ir/focus_priority/{id}', 'API\IncidentReportingController@deleteFocusPriority');
    Route::get('ir/impact_potentials', 'API\IncidentReportingController@listImpactPotentials');
    Route::post('ir/impact_potential', 'API\IncidentReportingController@addImpactPotential');
    Route::put('ir/impact_potential/{id}', 'API\IncidentReportingController@editImpactPotential');
    Route::delete('ir/impact_potential/{id}', 'API\IncidentReportingController@deleteImpactPotential');
    Route::get('ir/process_states', 'API\IncidentReportingController@listProcessStates');
    Route::post('ir/process_state', 'API\IncidentReportingController@addProcessState');
    Route::put('ir/process_state/{id}', 'API\IncidentReportingController@editProcessState');
    Route::delete('ir/process_state/{id}', 'API\IncidentReportingController@deleteProcessState');
    Route::get('ir/resource_types', 'API\IncidentReportingController@listResourceTypes');
    Route::post('ir/resource_type', 'API\IncidentReportingController@addResourceType');
    Route::put('ir/resource_type/{id}', 'API\IncidentReportingController@editResourceType');
    Route::delete('ir/resource_type/{id}', 'API\IncidentReportingController@deleteResourceType');
    Route::get('ir/stakeholder_types', 'API\IncidentReportingController@listStakeholderTypes');
    Route::post('ir/stakeholder_type', 'API\IncidentReportingController@addStakeholderType');
    Route::put('ir/stakeholder_type/{id}', 'API\IncidentReportingController@editStakeholderType');
    Route::delete('ir/stakeholder_type/{id}', 'API\IncidentReportingController@deleteStakeholderType');
    Route::get('ir/ixarma/companies', 'API\IncidentReportingController@listIxarmaCompanies');
    Route::get('ir/ixarma/participants', 'API\IncidentReportingController@listIxarmaParticipants');

    Route::get('ir/team_roles', 'API\IncidentReportingController@listTeamRoles');
    Route::post('ir/team_role', 'API\IncidentReportingController@addTeamRole');
    Route::put('ir/team_role/{id}', 'API\IncidentReportingController@editTeamRole');
    Route::delete('ir/team_role/{id}', 'API\IncidentReportingController@deleteTeamRole');

    Route::get('ir/ab/{type_id}', [IncidentReportingController::class, 'listActionBoards']);
    Route::get('ir/ab/{type_id}/options', [IncidentReportingController::class, 'optionsActionBoards']);
    Route::post('ir/ab/{type_id}', [IncidentReportingController::class, 'storeActionBoard']);
    Route::post('ir/ab/{id}/clone', [IncidentReportingController::class, 'cloneActionBoard']);
    Route::put('ir/ab/{type_id}/{id}', [IncidentReportingController::class, 'updateActionBoard']);
    Route::delete('ir/ab/{id}', [IncidentReportingController::class, 'deleteActionBoard']);

    Route::get('ir/{type_id}/actions', [IncidentReportingController::class, 'actionsActionBoards']);
    Route::post('ir/actions', [IncidentReportingController::class, 'storeAction']);
    Route::put('ir/actions/{id}', [IncidentReportingController::class, 'updateAction']);
    Route::delete('ir/actions/{id}', [IncidentReportingController::class, 'deleteAction']);

    // files
    Route::get('file/{id}', 'API\FileController@find');
    Route::post('file', 'API\FileController@add');
    Route::delete('file/{id}', 'API\FileController@delete');

    Route::get('billing/internal', [InternalBillingController::class, 'index']);
    Route::post('billing/internal', [InternalBillingController::class, 'create']);
    Route::get('billing/internal/{id}', [InternalBillingController::class, 'find']);
    Route::put('billing/internal/{id}', [InternalBillingController::class, 'update']);
    Route::delete('billing/internal/{id}', [InternalBillingController::class, 'delete']);

    Route::get('limit_group/types', [LimitationGroupController::class, 'types']);
    Route::post('limit_group', [LimitationGroupController::class, 'create']);
    Route::delete('limit_group/{id}', [LimitationGroupController::class, 'delete']);
    Route::post('limit_group/client', [LimitationGroupController::class, 'addLimitationModel']);
    Route::delete('limit_group/client/{id}', [LimitationGroupController::class, 'removeLimitationModel']);
    Route::post('limit_group/employee', [LimitationGroupController::class, 'addCompanyUser']);
    Route::delete('limit_group/employee/{id}', [LimitationGroupController::class, 'removeCompanyUser']);

    Route::post('activities', [ActivityController::class, 'store']);
    Route::patch('activities/{id}', [ActivityController::class, 'update']);
    Route::delete('activities/{id}', [ActivityController::class, 'destroy']);
    Route::get('activities/types', [ActivityController::class, 'getTypes']);
    Route::delete('activities/types/{id}', [ActivityController::class, 'destroyType']);
    Route::patch('activities/types/{id}', [ActivityController::class, 'updateType']);
    Route::post('activities/types', [ActivityController::class, 'storeType']);
});

