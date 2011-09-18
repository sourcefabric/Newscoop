<?php
/**
 * HybridAuth
 * 
 * An open source Web based Single-Sign-On PHP Library used to authentificates users with
 * major Web account providers and accessing social and data apis at Google, Facebook,
 * Yahoo!, MySpace, Tumblr, Windows live ID, etc. 
 *
 * Copyright (c) 2009 (http://hybridauth.sourceforge.net)
 *
 * @package     Hybrid_Auth
 * @author      hybridAuth Dev Team
 * @copyright   Copyright (c) 2009, hybridAuth Dev Team.
 * @license     http://hybridauth.sourceforge.net/licenses.html under MIT and GPL
 * @link        http://hybridauth.sourceforge.net 
 */

// ------------------------------------------------------------------------

/**
 * Hybrid_Providers_Tumblr class, wrapper for Tumblr auth/api
 *
 * some code is based on Tumblr OAuth PHP LIB by Abraham Williams
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.1.0 
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_Tumblr.html
 * @link       http://apiwiki.Tumblr.com/Sign-in-with-Tumblr
 * @link       http://abrah.am
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
class Hybrid_Providers_Tumblr extends Hybrid_Provider_Model
{
   /**
	* the IDp api client (optional)
	*/
	var $api = NULL; 

	// --------------------------------------------------------------------

   /**
	* IDp wrappers initializer
	*
	* The main job of wrappers initializer is to performs (depend on the IDp api client it self): 
	*     - include some libs nedded by this provider,
	*     - check IDp key and secret,
	*     - set some needed parameters (stored in $this->params) by this IDp api client
	*     - create and setup an instance of the IDp api client on $this->api 
	*/
	function initialize() 
	{
		GLOBAL $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES;

		Hybrid_Logger::info( "Enter [{$this->providerId}]::initialize()" );

		if( empty( $this->config["keys"]["CONSUMER_KEY"] ) )
		{
			throw new Exception( "Your {$this->providerId} CONSUMER_KEY is set to NULL! Please check HybridAuth configuration file." );
		}

		if( empty( $this->config["keys"]["CONSUMER_SECRET"] ) )
		{
			throw new Exception( "Your {$this->providerId} CONSUMER_SECRET is set to NULL! Please check your HybridAuth configuration file." );
		}
 
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Tumblr/OAuth.php";  
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Tumblr/Tumblr.php";  

		// If we have an access token, set it
		if 
		(
			Hybrid_Auth::storage()->get( "hauth_session.Tumblr.oauth_access_token" )
		&& 
			Hybrid_Auth::storage()->get( "hauth_session.Tumblr.oauth_access_token_secret" ) 
		)
		{ 
			Hybrid_Logger::info( "Enter [{$this->providerId}]::initialize() resert tokens" );
			
			$this->api = new TumblrOAuth
						(
							$this->config["keys"]["CONSUMER_KEY"], 
							$this->config["keys"]["CONSUMER_SECRET"], 
							Hybrid_Auth::storage()->get( "hauth_session.Tumblr.oauth_access_token" ), 
							Hybrid_Auth::storage()->get( "hauth_session.Tumblr.oauth_access_token_secret" )  
						);
		} 

		Hybrid_Logger::info( "[{$this->providerId}] wrapper initialized", $this->api );
	}

	// --------------------------------------------------------------------

   /**
	* begin login step 
	*/
	function loginBegin()
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );

		Hybrid_Auth::storage()->delete( "hauth_session.Tumblr.oauth_access_token" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.Tumblr.oauth_access_token_secret" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.Tumblr.oauth_state" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.Tumblr.oauth_request_token" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.Tumblr.oauth_request_token_secret" ); 
		
	    /* Create TumblrOAuth object with app key/secret */
	    $this->api = new TumblrOAuth( $this->config["keys"]["CONSUMER_KEY"], $this->config["keys"]["CONSUMER_SECRET"] );
	    
		/* Request tokens from Tumblr */
	    $tokz = $this->api->getRequestToken();

		Hybrid_Logger::debug( "{$this->providerId} returned response", $tokz );

		if 
		(
			! isset( $tokz['oauth_token'] ) || ! is_string( $tokz['oauth_token'] )
		|| 
			! isset( $tokz['oauth_token_secret'] ) || ! is_string( $tokz['oauth_token_secret'] )
		)
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Request Token." );
		}

	    /* Save tokens for later */
	    $token = $tokz['oauth_token'];
		Hybrid_Auth::storage()->set( "hauth_session.Tumblr.oauth_state"	  			   , "start"             ); 
		Hybrid_Auth::storage()->set( "hauth_session.Tumblr.oauth_request_token"        , $tokz['oauth_token'] );
		Hybrid_Auth::storage()->set( "hauth_session.Tumblr.oauth_request_token_secret" , $tokz['oauth_token_secret'] ); 

		# redirect user to Tumblr authorisation web page
		$auth_url = $this->api->getAuthorizeURL( $token ); 
		Hybrid_Auth::redirect( $auth_url );
	}

	// --------------------------------------------------------------------

   /**
	* finish login step
	* 
	* fetch returned parameters by The IDp client
	*/ 
	function loginFinish()
	{

		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginFinish()" );
		
		Hybrid_Logger::debug( "{$this->providerId} returned _REQUEST", $_REQUEST );

		if ( Hybrid_Auth::storage()->get( "hauth_session.Tumblr.oauth_state" ) != "start" )
		{
			throw new Exception( "Authentification failed! Out of sequence." );
		}

		$oauth_token    = @ $_REQUEST['oauth_token'];
		$oauth_verifier = @ $_REQUEST['oauth_verifier'];

		if ( ! $oauth_token )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid OAuth Token." );
		}

		if ( ! $oauth_verifier )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid OAuth Verifier." );
		}
 
		/* Create TumblrOAuth object with app key/secret and token key/secret from default phase */
		$this->api = new TumblrOAuth
						(
							$this->config["keys"]["CONSUMER_KEY"], 
							$this->config["keys"]["CONSUMER_SECRET"], 
							Hybrid_Auth::storage()->get( "hauth_session.Tumblr.oauth_request_token" ), 
							Hybrid_Auth::storage()->get( "hauth_session.Tumblr.oauth_request_token_secret" ) 
						);

		/* Request access tokens from Tumblr */
		$response = $this->api->getAccessToken( null, $oauth_verifier );

		Hybrid_Logger::debug( "{$this->providerId} returned response", $response );
		Hybrid_Logger::debug( "{$this->providerId} api dump", $this->api );

		/* Save the access tokens */
		Hybrid_Auth::storage()->set( "hauth_session.twitter.oauth_access_token"        , $response['oauth_token'] );
		Hybrid_Auth::storage()->set( "hauth_session.twitter.oauth_access_token_secret" , $response['oauth_token_secret'] );
 
		Hybrid_Logger::info( "[{$this->providerId}]::loginFinish(), Set user to connected" );
		Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 1 );
		
		$this->getUserProfile();
	}

	// --------------------------------------------------------------------

   /**
	* sign out the user from the IDp if possible, and erase his local session  
	*/
	function logout()
	{ 
		Hybrid_Logger::info( "Enter [{$this->providerId}]::logout()" );

		// adapter::deconnect() => expireStorage
		$this->deconnect(); 

		// Dunno if Tumblr support force logout

		return TRUE;
	}

	// --------------------------------------------------------------------

   /**
	* load the user profile from the IDp api client 
	*/
	function getUserProfile()
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::getUserProfile()" );

		if ( ! Hybrid_Auth::hasSession() )
		{
			throw new Exception( "HybridAuth can't access user profile data. The current user have to sign in with [{$this->providerId}] before any request!" );
		}

		// Hybrid_Logger::info( "{$this->providerId} return the user profile only once after authentication." );
 
		/* Run request on twitter API as user. */
		$response = $this->api->OAuthRequest( 'http://www.tumblr.com/api/authenticate', array(), 'GET' );

		Hybrid_Logger::debug( "{$this->providerId} returned authenticate response", $response );
		
		if ( ! $response )
		{
			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), reSet user to deconnected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );
			
			throw new Exception( "Request failed! {$this->providerId} returned an invalid response." );
		}

		$data = new SimpleXMLElement( $response );

		Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), Get user data", $data );

		// the easy way (well 4 me at least)
		$xml2array = @ $this->xml2array($data);

		$this->user->providerUID         	= @ (string) $xml2array["children"]["tumblelog"][0]["attr"]["url"]; 
		$this->user->profile->displayName  	= @ (string) $xml2array["children"]["tumblelog"][0]["attr"]["name"]; 
		$this->user->profile->profileURL 	= @ (string) $xml2array["children"]["tumblelog"][0]["attr"]["url"]; 
		$this->user->profile->webSiteURL 	= @ (string) $xml2array["children"]["tumblelog"][0]["attr"]["url"]; 
		$this->user->profile->photoURL   	= @ (string) $xml2array["children"]["tumblelog"][0]["attr"]["avatar-url"]; 

		Hybrid_Logger::info( "[{$this->providerId}]::loginFinish(), set user data", $this->user );

		Hybrid_Auth::storage()->set( "hauth_session.user.data", $this->user ); 

		// if the the provider has failed to return the user profile
		if ( ! $this->user->providerUID )
		{
			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), reSet user to deconnected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			throw new Exception( "User profile request failed! The user was able to authenticate with {$this->providerId}, but the provider has failed to return the user profile." );
		}
	}

	// --------------------------------------------------------------------

   /**
	* load the current logged in user contacts list from the IDp api client 
	*
	* HybridAuth dont provide users contats on version 1.0.x
	*/
	function getUserContacts()
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::getUserContacts()" );
	}
	
	function xml2array($xml) { 
		$arXML=array(); 
		$arXML['name']=trim($xml->getName()); 
		$arXML['value']=trim((string)$xml); 
		$t=array(); 
		foreach($xml->attributes() as $name => $value){ 
			$t[$name]=trim($value); 
		} 
		$arXML['attr']=$t; 
		$t=array(); 
		foreach($xml->children() as $name => $xmlchild) { 
			$t[$name][]=$this->xml2array($xmlchild); //FIX : For multivalued node 
		} 
		$arXML['children']=$t; 
		return($arXML); 
	} 
}
