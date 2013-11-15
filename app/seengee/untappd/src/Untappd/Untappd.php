<?php namespace Untappd;

use Guzzle\Http\Client;

class Untappd{

	// protected properties
	protected $client_id = "";
	protected $client_secret = "";
	protected $redirect_url = "";
	protected $access_token = "";

	// public properties
	public $apiBase = "https://api.untappd.com/v4";
    public $authenticateURL = "https://untappd.com/oauth/authenticate/";
    public $authorizeURL = "https://untappd.com/oauth/authorize/";

	public function __construct($client_id, $client_secret, $redirect_url = null, $access_token = NULL) 
	{
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->redirect_url = $redirect_url;
		$this->access_token = $access_token;
	}

	public function getAuthenticateUrl()
	{
		return $this->authenticateURL."?client_id=".$this->client_id."&response_type=code&redirect_url=".$this->redirect_url;
	}

	public function getAuthoriseUrl($code)
	{
		return $this->authorizeURL . "?client_id=".$this->client_id."&client_secret=".$this->client_secret."&response_type=code&redirect_url=".$this->redirect_url."&code=".$code;
	}

}
