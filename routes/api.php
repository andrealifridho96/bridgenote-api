<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes By Andre Alifridho
|--------------------------------------------------------------------------
|
| Here is api routes
|
*/

Route::group([
  'middleware' => 'api', 'prefix' => 'bridgenote'
], function($router){

  Route::post('client', 'BridgenoteController@RegisterClient');
  Route::get('get', 'BridgenoteController@get');
  Route::post('insert', 'BridgenoteController@insert');
  Route::put('update', 'BridgenoteController@update');
  Route::delete('delete', 'BridgenoteController@delete');
  Route::post('register', 'RegisterController@register');
  Route::post('login', 'AuthController@login');
  Route::post('me', 'AuthController@me');
  Route::post('logout', 'AuthController@logout');
  Route::post('refresh', 'AuthController@refresh');

});
