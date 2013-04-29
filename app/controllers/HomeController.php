<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		return View::make('home.index');
	}

	public function getLocation()
	{

		$lat = Request::get('lat');
		$long = Request::get('long');
		$client_id = "TNKB4HVGPFWKGKZRA5SRG1UIOWCRJYEX0HCYYYKXK4VLP0DE";
		$client_secret = "EII0ESCU4ZSFM3IUW4RNDESZVB5V5QHMBSQNDEE2UNHHIESQ";
		$date = date("Ymd");

		// Create a client and provide a base URL
		$client = new \Guzzle\Http\Client('https://api.foursquare.com/');
		$url = '/v2/venues/search?ll='.$lat.','.$long.'&client_id='.$client_id.'&client_secret='.$client_secret.'&v='.$date.'&categoryId=4d4b7105d754a06376d81259';
		// Create a request with basic Auth
		$request = $client->get($url);
		// Send the request and get the response
		$response = $request->send();
		echo $response->getBody();
		// >>> {"type":"User", ...
		//echo $response->getHeader('Content-Length');
	}

	public function getUntappdInfo()
	{
		function average($array)
		{
			return (array_sum($array) / count($array));
		}

		$getthebeers = function($last = null,$beers = array()) use (&$getthebeers){

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

		$unsortedBeers = array();

		foreach($thebeers as $beer => $ratings)
		{
			$rating = average($ratings);
			$unsortedBeers[$beer] = $rating;
		}
		arsort($unsortedBeers);
	}

}