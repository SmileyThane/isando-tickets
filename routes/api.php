<?php

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('register', 'API\AuthController@register');
Route::post('login', 'API\AuthController@login');
Route::get('plans', 'API\AuthController@plans');
Route::get('time_zones', 'HomeController@getTimeZones');
Route::get('countries', 'HomeController@getCountries');
Route::get('/mail/receive/{type?}', 'HomeController@receiveMail')->name('receiveEmail');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('auth/check', 'Controller@checkAuth');
    Route::get('logout', 'API\AuthController@logout');
    Route::get('roles', 'API\UserController@roles');
    Route::patch('roles', 'API\UserController@updateRoles');

    Route::group(['middleware' => 'setLanguage'], function () {
        //user management
        Route::get('user', 'API\UserController@find');
        Route::get('user/find/{id}', 'API\UserController@find');
        Route::post('user', 'API\UserController@update');
        Route::post('user/invite', 'API\UserController@sendInvite');
        Route::post('user/is_active', 'API\UserController@changeIsActive');
        Route::get('user/roles/id', 'API\UserController@authorizedRoleIds');

        //company management
        Route::get('company/{id?}', 'API\CompanyController@find');
        Route::post('company/{id}', 'API\CompanyController@update');
        Route::post('company/product', 'API\CompanyController@attachProduct');
        Route::get('main_company_name', 'API\CompanyController@mainCompanyName');

        //employee management
        Route::get('employee', 'API\CompanyController@getIndividuals');
        Route::post('company/{id}/employee', 'API\CompanyController@invite');
        Route::delete('company/employee/{id}', 'API\CompanyController@removeEmployee');

        //phone management
        Route::get('phone_types', 'API\PhoneController@getTypes');
        Route::post('phone', 'API\PhoneController@add');
        Route::patch('phone/{id}', 'API\PhoneController@update');
        Route::delete('phone/{id}', 'API\PhoneController@delete');

        //social management
        Route::get('social_types', 'API\SocialController@getTypes');
        Route::post('social', 'API\SocialController@add');
        Route::patch('social/{id}', 'API\SocialController@update');
        Route::delete('social/{id}', 'API\SocialController@delete');

        //address management
        Route::get('address_types', 'API\AddressController@getTypes');
        Route::post('address', 'API\AddressController@add');
        Route::patch('address/{id}', 'API\AddressController@update');
        Route::delete('address/{id}', 'API\AddressController@delete');

        //client management
        Route::get('client', 'API\ClientController@get');
        Route::get('supplier', 'API\ClientController@suppliers');
        Route::post('client', 'API\ClientController@create');
        Route::get('client/{id}', 'API\ClientController@find');
        Route::patch('client/{id}', 'API\ClientController@update');
        Route::delete('client/{id}', 'API\ClientController@delete');
        Route::post('client/employee', 'API\ClientController@attach');
        Route::delete('client/employee/{id}', 'API\ClientController@detach');

        //team management
        Route::get('team', 'API\TeamController@get');
        Route::post('team', 'API\TeamController@create');
        Route::get('team/{id}', 'API\TeamController@find');
        Route::patch('team/{id}', 'API\TeamController@update');
        Route::delete('team/{id}', 'API\TeamController@delete');
        Route::post('team/employee', 'API\TeamController@attach');
        Route::delete('team/employee/{id}', 'API\TeamController@detach');

        //product management
        Route::get('product', 'API\ProductController@get');
        Route::post('product', 'API\ProductController@create');
        Route::get('product/{id}', 'API\ProductController@find');
        Route::patch('product/{id}', 'API\ProductController@update');
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
        Route::delete('ticket/{id}', 'API\TicketController@delete');
        Route::post('ticket/{id}/team', 'API\TicketController@attachTeam');
        Route::post('ticket/{id}/employee', 'API\TicketController@attachEmployee');
        Route::post('ticket/{id}/contact', 'API\TicketController@attachContact');
        Route::post('ticket/{id}/answer', 'API\TicketController@addAnswer');
        Route::post('ticket/{id}/notice', 'API\TicketController@addNotice');
        Route::get('ticket_priorities', 'API\TicketController@priorities');
        Route::get('ticket_categories', 'API\TicketController@categories');
        Route::post('merge/ticket', 'API\TicketController@addMerge');
        Route::post('link/ticket', 'API\TicketController@addLink');
    });

    //language management
    Route::get('lang', 'API\LanguageController@all');
    Route::get('lang/map/{id?}', 'API\LanguageController@find');
});

