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

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', 'API\AuthController@logout');

    //user management
    Route::get('user', 'API\UserController@find');
    Route::post('user', 'API\UserController@update');

    //company management
    Route::get('company/{id?}', 'API\CompanyController@find');
    Route::post('company/employee', 'API\CompanyController@invite');
    Route::post('company/product', 'API\CompanyController@attachProduct');

    //client management
    Route::get('client', 'API\ClientController@get');
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
    Route::get('product', 'API\TeamController@get');
    Route::post('product', 'API\TeamController@create');
    Route::get('product/{id}', 'API\TeamController@find');
    Route::patch('product/{id}', 'API\TeamController@update');
    Route::delete('product/{id}', 'API\TeamController@delete');
    Route::post('product/employee', 'API\TeamController@attachEmployee');
    Route::delete('product/employee/{id}', 'API\TeamController@detachEmployee');
    Route::post('product/client', 'API\TeamController@attachClient');
    Route::delete('product/client/{id}', 'API\TeamController@detachClient');
    
});

