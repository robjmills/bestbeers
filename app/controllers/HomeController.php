<?php

class HomeController extends BaseController {

	// Foursquare configs
	public $fs_client_id = "TNKB4HVGPFWKGKZRA5SRG1UIOWCRJYEX0HCYYYKXK4VLP0DE";
	public $fs_client_secret = "EII0ESCU4ZSFM3IUW4RNDESZVB5V5QHMBSQNDEE2UNHHIESQ";
	public $fs_category_id = "4d4b7105d754a06376d81259";

	// Untappd configs
	public $ut_client_id = 'B38AE3C52EA3BE85AC98F58FB882FA1B296F1D18';
	public $ut_client_secret = '6F0169254D544BA4B41B09D20FCBAEA74ACE6339';
	public $ut_result_limit = "50";

	public function showWelcome()
	{
		return View::make('home.index');
	}

	public function getLocation()
	{

		$lat = Request::get('lat');
		$long = Request::get('long');
		$date = date("Ymd");

		// Create a client and provide a base URL
		$client = new \Guzzle\Http\Client('https://api.foursquare.com/');
		$url = '/v2/venues/search?ll='.$lat.','.$long.'&client_id='.$this->fs_client_id.'&client_secret='.$this->fs_client_secret.'&v='.$date.'&categoryId='.$this->fs_category_id;
		// Create a request with basic Auth
		$request = $client->get($url);
		// Send the request and get the response
		$response = $request->send();
		$body = $response->getBody();
		return $body;
	}

	public function getUntappdInfo($venue_id)
	{
		function average($array)
		{
			return (array_sum($array) / count($array));
		}

		$client = new Guzzle\Http\Client('http://api.untappd.com/v4/');		
		$requestString = 'venue/foursquare_lookup/'.$venue_id.'?client_id='. $this->ut_client_id .'&client_secret='. $this->ut_client_secret;
		$request = $client->get($requestString);
		$response = $request->send();
		$responses = json_decode($response->getBody(),true);
		$venue_id = $responses['response']['venue']['items'][0]['venue_id'];

		$getthebeers = function($venue_id, $last = null,$beers = array()) use (&$getthebeers){

			$client = new Guzzle\Http\Client('http://api.untappd.com/v4/');		
			$requestString = '/v4/venue/checkins/'.$venue_id.'?limit='. $this->ut_result_limit .'&client_id='. $this->ut_client_id .'&client_secret='. $this->ut_client_secret;

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
	}

}