<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

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

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\api'], function() {
    Route::get('invite/{email}', 'UserController@invite')->name('invite-user');
    Route::post('signup/{token}', 'UserController@signup')->name('signup');
    Route::post('verify/{code}', 'UserController@verify')->name('verify');

    Route::post('login', 'UserController@login')->name('login');
    Route::group(['middleware' => 'auth:sanctum'], function(){
        Route::post('profile', 'UserController@update')->name('update');
        Route::get('logout', 'UserController@logout')->name('logout');
    });
});

