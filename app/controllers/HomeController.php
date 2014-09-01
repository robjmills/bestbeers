<?php

use Untappd\Untappd;

class HomeController extends BaseController {

	/**
     * Foursquare categories
     * Food - 4d4b7105d754a06374d81259
     * Nightlife spot - 4d4b7105d754a06376d81259
     * Bar - 4bf58dd8d48988d116941735 (inside Nightlife spot)
     * pub - 4bf58dd8d48988d11b941735
     * Music venue - 4bf58dd8d48988d1e5931735
     *
     */
	public $fs_category_id = ["4bf58dd8d48988d116941735","4bf58dd8d48988d11b941735"];
    
    /**
     * @var
     */
    private $untappd;

    /**
     * @var array
     */
    private $beers = [];

    /**
     * store the number of 0 starred reviews so this can be used to adjust the weighted mean (maybe)
     * @var int
     */
    private $weightAdjust = 0;

    public function __construct()
    {
        $this->untappd = new Untappd([
            'client_id' 	=> getenv('UNTAPPD_CLIENT_ID'),
            'client_secret' => getenv('UNTAPPD_CLIENT_SECRET')
        ]);
    }
    
	public function showHome()
	{
        if ( !Session::get('utauth') ) {
            return Redirect::to('/login');
        }
		return View::make('home.index');
	}

    /**
     * Get foursquare list of local venues based on lat/long and category list
     * @return \GuzzleHttp\Stream\StreamInterface|null
     */
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
                'v'             => '20140806',
                'categoryId'    => implode(',',$this->fs_category_id),
                'radius'        => '400'
             ]
        ]);
		$body = $request->getBody();
		return $body;
	}

    /**
     * translate the foursquare venue to the untappd venue
     * @param $venue_id
     * @return mixed
     */
    private function foursquareToUntappd($venue_id)
    {
        $responses = $this->untappd->query('venue/foursquare_lookup/'.$venue_id,[
            'client_id'     => getenv('FOURSQUARE_CLIENT_ID'),
            'client_secret' => getenv('FOURSQUARE_CLIENT_SECRET'),
            'access_token'  => Session::get('utauth')
        ]);

        // venue ID used by Untappd
        $utVenueId = $responses['response']['venue']['items'][0]['venue_id'];

        return $utVenueId;
    }

    /**
     * Sort result set into beer name with average rating
     * @todo look into weighted mean
     * @return float
     */
    private function sortBeers()
    {
        $ratedBeers = [];
        foreach($this->beers as $beer => $ratings)
        {
            $avg = array_sum($ratings) / count($ratings);
            $ratedBeers[$beer] = $avg;
        }
        arsort($ratedBeers);
        $beerlist = [];
        foreach($ratedBeers as $k=>$v){
            $beer = explode('|',$k);
            $beerlist[] = [
                'name'  => $beer[0],
                'brewery'  => $beer[1],
                'rating'    =>  $v
            ];
        }
        return $beerlist;
    }

    /**
     * Get checkin information from Untappd venue by ID
     * @param $venue_id
     * @return mixed
     */
    public function getUntappdInfo($venue_id)
	{
        // stupid code that isn't as clever as it thinks it is
		$getthebeers = function($venue_id, $checkin_id = null) use (&$getthebeers){

            $params = [
                'client_id'     => getenv('FOURSQUARE_CLIENT_ID'),
                'client_secret' => getenv('FOURSQUARE_CLIENT_SECRET'),
                'ut_result_limit' => 50,
                'access_token'  => Session::get('utauth')
            ];

            if($checkin_id != null){
                $params = ($params + ['min_id' => $checkin_id]);
            }

            $responses = $this->untappd->query('venue/checkins/'.$venue_id, $params);

			foreach($responses['response']['checkins']['items'] as $checkin){
				if($checkin['rating_score'] > 0){
					$beer_key = $checkin['beer']['beer_name'].'|'.$checkin['brewery']['brewery_name'];
					//$beer_key = $checkin['beer']['bid'];
					$this->beers[$beer_key][] = $checkin['rating_score'];
					$checkin_id = $checkin['checkin_id'];
				} else{
                    $this->weightAdjust ++;
                }
			}

			if($checkin_id == null){
				return $getthebeers($venue_id, $checkin_id);
			}else{
				return $this->beers;
			}
		};
        $venue_id = $this->foursquareToUntappd($venue_id);
		$getthebeers($venue_id);

        $sortedBeers = $this->sortBeers();

        return Response::Json($sortedBeers);
	}

}