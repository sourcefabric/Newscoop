<?php
/**
 * HybridAuth
 * 
 * An open source Web based Single-Sign-On PHP Library used to authentificates users with
 * major Web account providers and accessing social and data apis at Google, Facebook,
 * Yahoo!, MySpace, Twitter, Windows live ID, etc. 
 *
 * Copyright (c) 2009 (http://hybridauth.sourceforge.net)
 *
 * @package     Hybrid_Auth
 * @author      hybridAuth Dev Team
 * @copyright   Copyright (c) 2009, hybridAuth Dev Team.
 * @license     http://hybridauth.sourceforge.net/licenses.html under MIT and GPL
 * @link        http://hybridauth.sourceforge.net
 * @since       HybridAuth 1.0.2 
 */

// ------------------------------------------------------------------------

	/**
	* A simple OAuth consumer slightly modified by Zachy <hybridauth@gmail.com> 
	*
	* original author: Daniel Hofstetter (http://cakebaker.42dh.com) 
	*/

// ------------------------------------------------------------------------

/**
 * A simple OAuth consumer for CakePHP.
 * 
 * Requires the OAuth library from http://oauth.googlecode.com/svn/code/php/
 * 
 * Copyright (c) by Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 */

// using an underscore in the class name to avoid a naming conflict with the OAuth library
class OAuth_Consumer { 
	private $url = null;
	private $consumerKey = null;
	private $consumerSecret = null;
	private $fullResponse = null;
	
	public function __construct($consumerKey, $consumerSecret = '') {
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
	}

	/**
	 * Call API with a GET request
	 */
	public function get($accessTokenKey, $accessTokenSecret, $url, $getData = array()) {
		$accessToken = new OAuthToken($accessTokenKey, $accessTokenSecret);
		$request = $this->createRequest('GET', $url, $accessToken, $getData);
		
		return $this->doGet($request->to_url());
	}
	
	public function getAccessToken($accessTokenURL, $requestToken, $httpMethod = 'POST', $parameters = array()) {
		$this->url = $accessTokenURL;
		$queryStringParams = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);
		$parameters['oauth_verifier'] = $queryStringParams['oauth_verifier'];
		$request = $this->createRequest($httpMethod, $accessTokenURL, $requestToken, $parameters);
		
		return $this->doRequest($request);
	}
	
	/**
	 * Useful for debugging purposes to see what is returned when requesting a request/access token.
	 */
	public function getFullResponse() {
		return $this->fullResponse;
	}
	
	/**
	 * @param $requestTokenURL
	 * @param $callback An absolute URL to which the Service Provider will redirect the User back when the Obtaining User 
	 * 					Authorization step is completed. If the Consumer is unable to receive callbacks or a callback URL 
	 * 					has been established via other means, the parameter value MUST be set to oob (case sensitive), to 
	 * 					indicate an out-of-band configuration. Section 6.1.1 from http://oauth.net/core/1.0a
	 * @param $httpMethod 'POST' or 'GET'
	 * @param $parameters
	 */
	public function getRequestToken($requestTokenURL, $callback = 'oob', $httpMethod = 'POST', $parameters = array()) {
		$this->url = $requestTokenURL;
		$parameters['oauth_callback'] = $callback;
		$request = $this->createRequest($httpMethod, $requestTokenURL, null, $parameters);
		
		return $this->doRequest($request);
	}
	
	/**
	 * Call API with a POST request
	 */
	public function post($accessTokenKey, $accessTokenSecret, $url, $postData = array()) {
		$accessToken = new OAuthToken($accessTokenKey, $accessTokenSecret);
		$request = $this->createRequest('POST', $url, $accessToken, $postData);
		
		return $this->doPost($url, $request->to_postdata());
	}
	
	protected function createOAuthToken($response) {
		if (isset($response['oauth_token']) && isset($response['oauth_token_secret'])) {
			return new OAuthToken($response['oauth_token'], $response['oauth_token_secret']);
		}
		
		return null;
	}
	
	private function createConsumer() {
		return new OAuthConsumer($this->consumerKey, $this->consumerSecret);
	}
	
	private function createRequest($httpMethod, $url, $token, array $parameters) {
		$consumer = $this->createConsumer();
		$request = OAuthRequest::from_consumer_and_token($consumer, $token, $httpMethod, $url, $parameters);
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
		
		return $request;
	}
	
	private function doRequest($request) {
		if ($request->get_normalized_http_method() == 'POST') {
			$data = $this->doPost($this->url, $request->to_postdata());
		} else {
			$data = $this->doGet($request->to_url());
		}

		$this->fullResponse = $data;
		$response = array();
		parse_str($data, $response);

		return $this->createOAuthToken($response);
	}

	private function doGet($url) { 
		return HttpClient::quickGet($url);
	}
	
	private function doPost($url, $data) { 
		return HttpClient::quickPost($url, $data);
	}
}