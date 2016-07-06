<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::post('search', ['as' => 'search', 'uses' => 'SearchController@search']);

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController'
]);

Route::get('/', function () {
    return view('welcome');
});


Route::group(['middleware' => 'auth'], function () {

    Route::get('/admin', 'SearchController@index');
    Route::get('/search/excluir/{id}', ['as' => 'excluir', 'uses' => 'SearchController@excluir']);

});

