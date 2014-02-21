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

Route::get('/', function(){

	dd(Config::get('app.url').'/authenticate');

	$untappd = new Untappd([
		'client_id' 	=> 'B38AE3C52EA3BE85AC98F58FB882FA1B296F1D18',
		'client_secret' => '6F0169254D544BA4B41B09D20FCBAEA74ACE6339',
		'redirect_url'	=> Config::get('app.url').':8000/authenticate'
	]);
	$redirect = $untappd->getAuthenticateUrl();
	return Redirect::to($redirect);
});

Route::get('/authenticate', function(){

	// get oauth code returned
	$authcode =  Request::get('code');
	
	// request access token
	$untappd = new Untappd([
		'client_id' 	=> 'B38AE3C52EA3BE85AC98F58FB882FA1B296F1D18',
		'client_secret' => '6F0169254D544BA4B41B09D20FCBAEA74ACE6339',
		'redirect_url'	=> Config::get('app.url').':8000/authenticate'
	]);
	$redirect = $untappd->getAuthoriseUrl($authcode);
	$untappd->authorise($redirect);

	$responses = $untappd->getCommand('venue/checkins/448060/',[
		'limit' => '100'
	]);

	foreach($responses['response']['checkins']['items'] as $checkin){
		if($checkin['rating_score'] > 0){
			$beer_key = $checkin['beer']['beer_name'].' by '.$checkin['brewery']['brewery_name'];
			$beers[$beer_key][] = $checkin['rating_score'];
			$checkin_id = $checkin['checkin_id'];
		}
	}
	dd($beers);

});

/*
Route::get('/', 'HomeController@showWelcome');
Route::get('/location', 'HomeController@getLocation');
Route::get('/beers/{venue_id}', 'HomeController@getUntappdInfo');
*/
// LOOK INTO weighted mean / weighted average
