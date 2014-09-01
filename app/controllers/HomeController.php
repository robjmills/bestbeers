<?php

use Untappd\Untappd;

class HomeController extends BaseController {

	/** Foursquare configs
     * Food - 4d4b7105d754a06374d81259
     * Nightlife spot - 4d4b7105d754a06376d81259
     * Bar - 4bf58dd8d48988d116941735 (inside Nightlife spot)
     * Music venue - 4bf58dd8d48988d1e5931735
     */
	public $fs_category_id = ["4d4b7105d754a06376d81259","4bf58dd8d48988d1e5931735"]; // music venue

	public function showHome()
	{
        if ( !Session::get('utauth') ) {
            return Redirect::to('/login');
        }
		return View::make('home.index');
	}

	public function getFoursquareVenue()
	{
		$lat = Input::get('lat');
		$long = Input::get('long');
		$date = date("Ymd");

		// Create a Guzzle Client
		$client = new GuzzleHttp\Client();

		// Create a request with basic Auth
		$request = $client->get('https://api.foursquare.com/v2/venues/search',[
            'query' => [
                'll'            => $lat.','.$long,
                'client_id'     => getenv('FOURSQUARE_CLIENT_ID'),
                'client_secret' => getenv('FOURSQUARE_CLIENT_SECRET'),
                'v'             => $date,
                'categoryId'    => implode(',',$this->fs_category_id)
             ]
        ]);
		$body = $request->getBody();
		return $body;
	}

	public function getUntappdInfo($venue_id)
	{
        $untappd = new Untappd([
            'client_id' 	=> getenv('UNTAPPD_CLIENT_ID'),
            'client_secret' => getenv('UNTAPPD_CLIENT_SECRET')
        ]);

        // translate the foursquare venue to the untappd venue
        $responses = $untappd->query('venue/foursquare_lookup/'.$venue_id,[
            'client_id'     => getenv('FOURSQUARE_CLIENT_ID'),
            'client_secret' => getenv('FOURSQUARE_CLIENT_SECRET'),
            'access_token'  => Session::get('utauth')
        ]);

        // venue ID used by Untappd
        $utVenueId = $responses['response']['venue']['items'][0]['venue_id'];

        // stupid code that isn't as clever as it thinks it is
		$getthebeers = function(Untappd $untappd, $venue_id, $checkin_id = null,$beers = []) use (&$getthebeers){

            $params = [
                'client_id'     => getenv('FOURSQUARE_CLIENT_ID'),
                'client_secret' => getenv('FOURSQUARE_CLIENT_SECRET'),
                'ut_result_limit' => 50,
                'access_token'  => Session::get('utauth')
            ];

            if($checkin_id != null){
                $params = ($params + ['min_id' => $checkin_id]);
            }

            $responses = $untappd->query('venue/checkins/'.$venue_id, $params);

			foreach($responses['response']['checkins']['items'] as $checkin){
				if($checkin['rating_score'] > 0){
					$beer_key = $checkin['beer']['beer_name'].' by '.$checkin['brewery']['brewery_name'];
					//$beer_key = $checkin['beer']['bid'];
					$beers[$beer_key][] = $checkin['rating_score'];
					$checkin_id = $checkin['checkin_id'];
				}
			}

			if($checkin_id == null){
				return $getthebeers($untappd, $venue_id, $checkin_id, $beers);
			}else{
				return $beers;
			}
		};
		$thebeers = $getthebeers($untappd, $utVenueId);

        return Response::Json($thebeers);
        /*
		$unsortedBeers = array();

		foreach($thebeers as $beer => $ratings)
		{
			$rating = average($ratings);
			$unsortedBeers[$beer] = $rating;

			$client = new GuzzleHttp\Client('http://api.untappd.com/v4/');
			$requestString = '/v4/beer/info/'.$beer.'?client_id='. $this->ut_client_id .'&client_secret='. $this->ut_client_secret;
			$request = $client->get($requestString);
			$response = $request->send();
			$responses = json_decode($response->getBody(),true);
			$cleanresponse = $responses['response']['beer'];
			$overallrating = $cleanresponse['rating_score'];
			$ratings = array($ratings,$overallrating);
			$beerdeets = $cleanresponse['beer_name'].' by '.$cleanresponse['brewery']['brewery_name'];
			$unsortedBeers[$beerdeets] = $ratings;

		}
		arsort($unsortedBeers);
		return Response::Json($unsortedBeers);
        */
	}

}