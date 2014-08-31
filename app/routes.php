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

Route::get('/', 'HomeController@showHome');
Route::get('/location', 'HomeController@getFoursquareVenue');
Route::get('/beers/{venue_id}', 'HomeController@getUntappdInfo');

Route::get('/login', function(){

	$untappd = new Untappd([
		'client_id' 	=> $_ENV['UNTAPPD_CLIENT_ID'],
		'client_secret' => $_ENV['UNTAPPD_CLIENT_SECRET'],
		'redirect_url'	=> Config::get('app.url').'/authenticate'
	]);
	$redirect = $untappd->getAuthenticateUrl();
	return Redirect::to($redirect);
});

Route::get('/authenticate', function(){

	// get oauth code returned
	$authcode =  Request::get('code');
	
	// request access token
	$untappd = new Untappd([
        'client_id' 	=> $_ENV['UNTAPPD_CLIENT_ID'],
        'client_secret' => $_ENV['UNTAPPD_CLIENT_SECRET'],
		'redirect_url'	=> Config::get('app.url').'/authenticate'
	]);
	$redirect = $untappd->getAuthoriseUrl($authcode);
	
	if($untappd->authorise($redirect) == true)
	{
        $token = $untappd->getAccessToken();
        Session::put('utauth',$token);
        return Redirect::to('/');
        /*
		$responses = $untappd->query('venue/checkins/448060/',[
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
        */
	}
	else
	{
		echo $untappd->getError();
	}

});

/*
Route::get('/', 'HomeController@showWelcome');
Route::get('/location', 'HomeController@getLocation');
Route::get('/beers/{venue_id}', 'HomeController@getUntappdInfo');
*/
// LOOK INTO weighted mean / weighted average
