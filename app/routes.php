<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'HomeController@showWelcome');
Route::get('/location', 'HomeController@getLocation');
Route::get('/beers/{venue_id}', 'HomeController@getUntappdInfo');
// LOOK INTO weighted mean / weighted average
