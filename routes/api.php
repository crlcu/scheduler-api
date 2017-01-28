<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Auth::loginUsingId(1);

// Controllers Within The "App\Http\Controllers\Api" Namespace

Route::middleware('firewall')->namespace('Api')->group(function ()
{
    Route::any('login', 'AuthenticationController@authenticate');

    Route::group(['middleware' => ['auth.basic']], function ()
    {
        Route::get('logout', 'AuthenticationController@logout');

        // Groups
        Route::group(['middleware' => 'role:manage-groups'], function ()
        {
            Route::resource('groups', 'GroupsController');
        });

        // Tasks
        Route::resource('tasks', 'TasksController');
        Route::get('tasks/{id}/executions', 'TaskExecutionsController@index');

        // Roles
        // Route::group(['middleware' => 'role:manage-roles'], function ()
        // {
            Route::resource('roles', 'RolesController');
        // });
    });
});
