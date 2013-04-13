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

#Route::controller('account', 'AccountController');
Route::controller('/', 'HomeController');


// LOOK INTO weighted mean / weighted average

Route::get('/', function()
{

	function average($array)
	{
		return (array_sum($array) / count($array));
	}

	$getthebeers = function($last = null,$beers = []) use (&$getthebeers){

		// vars
		$client_id = 'B38AE3C52EA3BE85AC98F58FB882FA1B296F1D18';
		$client_secret = '6F0169254D544BA4B41B09D20FCBAEA74ACE6339';
		$limit = '50';

		$client = new Guzzle\Http\Client('http://api.untappd.com/v4');		
		$requestString = '/v4/venue/checkins/448060?limit='. $limit .'&client_id='. $client_id .'&client_secret='. $client_secret;

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
			return $getthebeers($checkin_id, $beers);
		}else{
			return $beers;
		}
	};
	$thebeers = $getthebeers();

	var_dump($thebeers);

	$unsortedBeers = array();

	foreach($thebeers as $beer => $ratings)
	{
		$rating = average($ratings);
		$unsortedBeers[$beer] = $rating;
	}
	arsort($unsortedBeers);

	var_dump($unsortedBeers);die();

	foreach($unsortedBeers as $beer => $average)
	{
		echo $beer." => ".$average." => ".implode(',',$thebeers[$beer])."<br />";
	}
});
