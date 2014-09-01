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
		'client_id' 	=> getenv('UNTAPPD_CLIENT_ID'),
		'client_secret' => getenv('UNTAPPD_CLIENT_SECRET'),
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
        'client_id' 	=> getenv('UNTAPPD_CLIENT_ID'),
        'client_secret' => getenv('UNTAPPD_CLIENT_SECRET'),
		'redirect_url'	=> Config::get('app.url').'/authenticate'
	]);
	$redirect = $untappd->getAuthoriseUrl($authcode);
	
	if($untappd->authorise($redirect) == true)
	{
        // get access token and store in session
        $token = $untappd->getAccessToken();
        Session::put('utauth',$token);
        return Redirect::to('/');
	}
	else
	{
		echo $untappd->getError();
	}

});
