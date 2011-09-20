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
 */

// ------------------------------------------------------------------------

/**
 * Hybrid_Providers_Twitter class, wrapper for Twitter auth/api
 *
 * some code is based on Twitter OAuth PHP LIB by Abraham Williams
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.1
 * @since      HybridAuth 1.0.1 
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_Twitter.html
 * @link       http://apiwiki.twitter.com/Sign-in-with-Twitter
 * @link       http://abrah.am
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
class Hybrid_Providers_Twitter extends Hybrid_Provider_Model
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

		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Twitter/OAuth.php";  
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Twitter/Twitter.php";
	}

	// --------------------------------------------------------------------

   /**
	* begin login step 
	*/
	function loginBegin()
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );

		Hybrid_Auth::storage()->delete( "hauth_session.twitter.oauth_access_token" );
		Hybrid_Auth::storage()->delete( "hauth_session.twitter.oauth_access_token_secret" );
		Hybrid_Auth::storage()->delete( "hauth_session.twitter.oauth_state" );
		Hybrid_Auth::storage()->delete( "hauth_session.twitter.oauth_request_token" );
		Hybrid_Auth::storage()->delete( "hauth_session.twitter.oauth_request_token_secret" );

	    /* Create TwitterOAuth object with app key/secret */
	    $this->api = new TwitterOAuth( $this->config["keys"]["CONSUMER_KEY"], $this->config["keys"]["CONSUMER_SECRET"] );

		/* Request tokens from twitter */
	    $tokz = $this->api->getRequestToken(); 
		
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
		Hybrid_Auth::storage()->set( "hauth_session.twitter.oauth_state"	  			, "start"             ); 
		Hybrid_Auth::storage()->set( "hauth_session.twitter.oauth_request_token"        , $tokz['oauth_token'] );
		Hybrid_Auth::storage()->set( "hauth_session.twitter.oauth_request_token_secret" , $tokz['oauth_token_secret'] ); 

		# redirect user to twitter authorisation web page
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

		if ( Hybrid_Auth::storage()->get( "hauth_session.twitter.oauth_state" ) != "start" )
		{
			throw new Exception( "Authentification failed! Out of sequence." );
		}

		$oauth_token = @ $_REQUEST['oauth_token'];

		if ( ! $oauth_token )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid OAuth Token." );
		}

		Hybrid_Auth::storage()->set( "hauth_session.twitter.oauth_state", "returned" ); 

		/* If the access tokens are already set skip to the API call */
		if 
		( 
				! Hybrid_Auth::storage()->get( "hauth_session.twitter.oauth_access_token" ) 
			&& 
				! Hybrid_Auth::storage()->get( "hauth_session.twitter.oauth_access_token_secret" ) 
		) 
		{
			/* Create TwitterOAuth object with app key/secret and token key/secret from default phase */
			$this->api = new TwitterOAuth
							(
								$this->config["keys"]["CONSUMER_KEY"], 
								$this->config["keys"]["CONSUMER_SECRET"], 
								Hybrid_Auth::storage()->get( "hauth_session.twitter.oauth_request_token" ), 
								Hybrid_Auth::storage()->get( "hauth_session.twitter.oauth_request_token_secret" ) 
							);

			/* Request access tokens from twitter */
			$tokz = $this->api->getAccessToken();

			/* Save the access tokens. Normally these would be saved in a database for future use. */
			Hybrid_Auth::storage()->set( "hauth_session.twitter.oauth_access_token"        , $tokz['oauth_token'] );
			Hybrid_Auth::storage()->set( "hauth_session.twitter.oauth_access_token_secret" , $tokz['oauth_token_secret'] );
		}

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

		// Dunno if twitter support force logout

		return TRUE;
	}

	// --------------------------------------------------------------------

   /**
	* load the user profile from the IDp api client
	*/
	function getUserProfile()
	{ 

		Hybrid_Logger::info( "Enter [{$this->providerId}]::getUserProfile()" );

		# to access this area, the user have to be loggedin with and IDp an has a HybridAuth session
		if ( ! Hybrid_Auth::hasSession() )
		{
			throw new Exception( "HybridAuth can't access user profile data. The current user have to sign in with [{$this->providerId}] before any request!" );
		}

		$this->api = new TwitterOAuth
						(
							$this->config["keys"]["CONSUMER_KEY"], 
							$this->config["keys"]["CONSUMER_SECRET"], 
							Hybrid_Auth::storage()->get( "hauth_session.twitter.oauth_access_token" ), 
							Hybrid_Auth::storage()->get( "hauth_session.twitter.oauth_access_token_secret" ) 
						);

		/* Run request on twitter API as user. */ 
		$response = $this->api->get('account/verify_credentials'); 
 
		if ( ! $response )
		{
			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), reSet user to deconnected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			throw new Exception( "User profile request failed! {$this->providerId} returned an invalid response." );
		} 

		Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), Get user data", $response );

		# store the user profile. 
		$this->user->providerUID            = @ $response->id;
		$this->user->profile->displayName  	= @ $response->screen_name;
		$this->user->profile->description  	= @ $response->description;
		$this->user->profile->firstName  	= @ $response->name; 
		$this->user->profile->photoURL   	= @ $response->profile_image_url;
		$this->user->profile->profileURL 	= @ 'http://twitter.com/' . $response->screen_name;
		$this->user->profile->webSiteURL 	= @ $response->url; 
		$this->user->profile->address 		= @ $response->location;

		Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), set user data", $this->user );

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
}
