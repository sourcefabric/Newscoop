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
 * Hybrid_Providers_MySpace class, wrapper for MySpaceID 
 *
 * some code is based on MySpaceID - PHP - SDK
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.1
 * @since      HybridAuth 1.0.1 
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_MySpace.html
 * @link       http://code.google.com/p/myspaceid-php-sdk/
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
class Hybrid_Providers_MySpace extends Hybrid_Provider_Model
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
			throw new Exception( "CONSUMER_KEY of [{$this->providerId}] is set to NULL!" );
		}

		if( empty( $this->config["keys"]["CONSUMER_SECRET"] ) )
		{
			throw new Exception( "CONSUMER_SECRET of [{$this->providerId}] is set to NULL!" );
		}

		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "MySpaceID/MySpaceID.php"; 
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Auth/OpenID/CryptUtil.php"; 
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "MySpaceID/OAuth.php"; 

		// If we have an access token, set it
		if 
		(
			Hybrid_Auth::storage()->get( "hauth_session.myspaceid.access_token" )
		&& 
			Hybrid_Auth::storage()->get( "hauth_session.myspaceid.access_secret" ) 
		)
		{
			$this->api = new MySpace
						(
							$this->config["keys"]["CONSUMER_KEY"], 
							$this->config["keys"]["CONSUMER_SECRET"], 
							Hybrid_Auth::storage()->get( "hauth_session.myspaceid.access_token" ), 
							Hybrid_Auth::storage()->get( "hauth_session.myspaceid.access_secret" ) 
						); 
		}
	}

	// --------------------------------------------------------------------

   /**
	* begin login step
	* 
	* get a request token + secret from MySpace and redirect to the authorization page 
	*/
	function loginBegin()
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );

		Hybrid_Auth::storage()->delete( "hauth_session.myspaceid.access_token" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.myspaceid.access_secret" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.myspaceid.request_token" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.myspaceid.request_secret" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.myspaceid.callback_confirmed" ); 

		# init new MySpace obj with key + secret
		$this->api = new MySpace( $this->config["keys"]["CONSUMER_KEY"], $this->config["keys"]["CONSUMER_SECRET"] ); 

		# reqest a token from myspaceid api
		$tokz = $this->api->getRequestToken( Hybrid_Auth::storage()->get( "hauth_session.hauth_endpoint" ) );

		if 
		(
			! isset( $tokz['oauth_token'] ) || ! is_string( $tokz['oauth_token'] )
		|| 
			! isset( $tokz['oauth_token_secret'] ) || ! is_string( $tokz['oauth_token_secret'] )
		)
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Request Token." );
		}

		Hybrid_Auth::storage()->set( "hauth_session.myspaceid.request_token"      , $tokz['oauth_token'] );
		Hybrid_Auth::storage()->set( "hauth_session.myspaceid.request_secret"	  , $tokz['oauth_token_secret'] );
		Hybrid_Auth::storage()->set( "hauth_session.myspaceid.callback_confirmed" , $tokz['oauth_callback_confirmed'] ); 

		# redirect user to MySpace authorisation web page
		$auth_url = $this->api->getAuthorizeURL( $tokz['oauth_token'] ); 

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

		$oauth_verifier = @ $_REQUEST['oauth_verifier'];

		if ( ! $oauth_verifier )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Access Token." );
		}

		$this->api = new MySpace
					( 
						$this->config["keys"]["CONSUMER_KEY"], 
						$this->config["keys"]["CONSUMER_SECRET"],
						Hybrid_Auth::storage()->get( "hauth_session.myspaceid.request_token" ), 
						Hybrid_Auth::storage()->get( "hauth_session.myspaceid.request_secret" ), 
						TRUE, 
						$oauth_verifier
					);

		$tokz = $this->api->getAccessToken();

		if ( ! is_string($tokz->key) || ! is_string($tokz->secret) )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Access Token." );
		}

		Hybrid_Auth::storage()->set( "hauth_session.myspaceid.access_token"  , $tokz->key    ); 
		Hybrid_Auth::storage()->set( "hauth_session.myspaceid.access_secret" , $tokz->secret ); 

		// we have our access token + secret, so now we can actually *use* the api
		$this->api = new MySpace
					(
						$this->config["keys"]["CONSUMER_KEY"], 
						$this->config["keys"]["CONSUMER_SECRET"], 
						Hybrid_Auth::storage()->get( "hauth_session.myspaceid.access_token" ), 
						Hybrid_Auth::storage()->get( "hauth_session.myspaceid.access_secret" ) 
					);

		Hybrid_Logger::info( "[{$this->providerId}]::loginFinish(), Set user to connected" );
		Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 1 );

		# store the user id. 
		$this->user->providerUID = $this->api->getCurrentUserId( );

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

		// MySpace DO NOT support force logout

		return TRUE;
	}

	// --------------------------------------------------------------------

   /**
	* load the user profile from the IDp api client
	*/
	function getUserProfile()
	{

		Hybrid_Logger::info( "Enter [{$this->providerId}]::getUserProfile()" );

		# to access this area, the user have to be loggedin with an IDp and has a HybridAuth session
		if ( ! Hybrid_Auth::hasSession() )
		{
			throw new Exception( "HybridAuth can't access user profile data. The current user have to sign in with [{$this->providerId}] before any request!" );
		}

		$data = $this->api->getProfile( $this->user->providerUID );

		Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), Get user data", $data );

		if ( ! is_object( $data ) )
		{
			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), reSet user to deconnected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			throw new Exception( "User profile request failed! {$this->providerId} returned an invalide response." );
		} 

		$this->user->profile->displayName  	= @ $data->basicprofile->name;
		$this->user->profile->description  	= @ $data->aboutme;
		$this->user->profile->gender     	= @ $data->basicprofile->gender;
		$this->user->profile->photoURL   	= @ $data->basicprofile->image;
		$this->user->profile->profileURL 	= @ $data->basicprofile->webUri;
		$this->user->profile->age 			= @ $data->age;
		$this->user->profile->country 		= @ $data->country;
		$this->user->profile->region 		= @ $data->region;
		$this->user->profile->city 			= @ $data->city;
		$this->user->profile->zip 			= @ $data->postalcode;

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
