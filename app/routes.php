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
Route::get('/beers/{venue_id}', function($venue_id)
{
		function average($array)
		{
			return (array_sum($array) / count($array));
		}

		$client_id = 'B38AE3C52EA3BE85AC98F58FB882FA1B296F1D18';
		$client_secret = '6F0169254D544BA4B41B09D20FCBAEA74ACE6339';	

		$client = new Guzzle\Http\Client('http://api.untappd.com/v4/');		
		$requestString = 'venue/foursquare_lookup/'.$venue_id.'?client_id='. $client_id .'&client_secret='. $client_secret;

		$request = $client->get($requestString);
		$response = $request->send();
		$responses = json_decode($response->getBody(),true);
		$venue_id = $responses['response']['venue']['items'][0]['venue_id'];

		$getthebeers = function($venue_id, $last = null,$beers = array()) use (&$getthebeers){

			// vars
			$client_id = 'B38AE3C52EA3BE85AC98F58FB882FA1B296F1D18';
			$client_secret = '6F0169254D544BA4B41B09D20FCBAEA74ACE6339';
			$limit = '50';

			$client = new Guzzle\Http\Client('http://api.untappd.com/v4/');		
			$requestString = '/v4/venue/checkins/'.$venue_id.'?limit='. $limit .'&client_id='. $client_id .'&client_secret='. $client_secret;

			if($last != null){
				$requestString .= '&min_id='.$last;	
			}

			$request = $client->get($requestString);
			$response = $request->send();
			$responses = json_decode($response->getBody(),true);

			foreach($responses['response']['checkins']['items'] as $checkin){
				if($checkin['rating_score'] > 0){
					$beer_key = $checkin['beer']['beer_name'].' by '.$checkin['brewery']['brewery_name'];
					$beers[$beer_key][] = $checkin['rating_score'];
					$checkin_id = $checkin['checkin_id'];
				}
			}

			if($last == null){			
				return $getthebeers($venue_id, $checkin_id, $beers);
			}else{
				return $beers;
			}
		};
		$thebeers = $getthebeers($venue_id);

		$unsortedBeers = array();

		foreach($thebeers as $beer => $ratings)
		{
			$rating = average($ratings);
			$unsortedBeers[$beer] = $rating;
		}
		arsort($unsortedBeers);
		return Response::Json($unsortedBeers);

});
// LOOK INTO weighted mean / weighted average
