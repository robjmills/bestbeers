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
use Untappd\Untappd;
use Guzzle\Http\Client;

Route::get('/', function(){
	$untappd = new Untappd(
		'B38AE3C52EA3BE85AC98F58FB882FA1B296F1D18',
		'6F0169254D544BA4B41B09D20FCBAEA74ACE6339',
		'http://beerhere.gopagoda.com/authenticate'
	);
	$redirect = $untappd->getAuthenticateUrl();
	return Redirect::to($redirect);
});

Route::get('/authenticate', function(){
	$authcode =  Request::get('code');
	$untappd = new Untappd(
		'B38AE3C52EA3BE85AC98F58FB882FA1B296F1D18',
		'6F0169254D544BA4B41B09D20FCBAEA74ACE6339',
		'http://beerhere.gopagoda.com/authorise'
	);
	$redirect = $untappd->getAuthoriseUrl($authcode);

	$client = new Client();
	$request = $client->get($redirect);
	$response = $request->send();
	$responses = json_decode($response->getBody(),true);
	dd($responses);
});

Route::get('/authorise', function(){
	return Request::all();
});

/*
Route::get('/', 'HomeController@showWelcome');
Route::get('/location', 'HomeController@getLocation');
Route::get('/beers/{venue_id}', 'HomeController@getUntappdInfo');
*/
// LOOK INTO weighted mean / weighted average
