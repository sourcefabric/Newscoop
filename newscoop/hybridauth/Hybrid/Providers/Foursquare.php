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
 * Hybrid_Providers_Foursquare class, wrapper for Foursquare auth/api
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.1.0 
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_Foursquare.html
 * @link       http://en.netlog.com/go/developer/documentation/
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
class Hybrid_Providers_Foursquare extends Hybrid_Provider_Model
{
   /**
	* the IDp api client (optional)
	*/
	var $api          = NULL; 
	var $redirect_uri = NULL; 

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
		GLOBAL $GLOBAL_HYBRID_AUTH_URL_EP;

		Hybrid_Logger::info( "Enter [{$this->providerId}]::initialize()" );

		if( empty( $this->config["keys"]["CLIENT_ID"] ) )
		{
			throw new Exception( "CLIENT_ID of [{$this->providerId}] is set to NULL!" );
		}

		if( empty( $this->config["keys"]["CLIENT_SECRET"] ) )
		{
			throw new Exception( "CLIENT_SECRET of [{$this->providerId}] is set to NULL!" );
		}

		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Foursquare/FoursquareAPI.php"; 

		$this->api          = new FoursquareAPI( $this->config["keys"]["CLIENT_ID"], $this->config["keys"]["CLIENT_SECRET"] );
		
		$this->redirect_uri = $GLOBAL_HYBRID_AUTH_URL_EP . ( strpos( $GLOBAL_HYBRID_AUTH_URL_EP, '?' ) ? '&' : '?' ) . "hauth.done=Foursquare";

		// If we have an access token, set it
		if 
		(
			Hybrid_Auth::storage()->get( "hauth_session.foursquare.oauth_access_token" ) 
		)
		{
			$this->api->SetAccessToken
				( 
					Hybrid_Auth::storage()->get( "hauth_session.foursquare.oauth_access_token" ) 
				);
		}
	}

	// --------------------------------------------------------------------

   /**
	* begin login step 
	*/
	function loginBegin()
	{
		GLOBAL $GLOBAL_HYBRID_AUTH_URL_EP;
	
		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );

		Hybrid_Auth::storage()->delete( "hauth_session.foursquare.oauth_access_token" );

		$authorize_link = $this->api->AuthenticationLink( urlencode( $this->redirect_uri ) );
 
		# redirect user to Foursquare authorisation web page
		Hybrid_Auth::redirect( $authorize_link ); 
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

		$code  = $_REQUEST['code'];
		
		$access_token = $this->api->GetToken( $code, urlencode( $this->redirect_uri ) );

		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginFinish(), recived GetToken", array( $code, $this->redirect_uri, $access_token ) );

		if ( ! $access_token )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Access Token." );
		}

		Hybrid_Auth::storage()->set( "hauth_session.foursquare.oauth_access_token", $access_token ); 

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

		// Dunno if this provider support force logout

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

		$response = $this->api->GetPrivate( "users/self", array() );
		$data     = json_decode( $response );
 
		Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), Get user data", $data );

		if ( ! is_object( $data ) )
		{
			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), reSet user to deconnected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			throw new Exception( "User profile request failed! {$this->providerId} returned an invalide response." );
		} 

		$this->user->providerUID         	= @ $data->response->user->id;
		$this->user->profile->firstName  	= @ $data->response->user->firstName;
		$this->user->profile->lastName  	= @ $data->response->user->lastName;
		$this->user->profile->displayName  	= @ $data->response->user->firstName . " " . $data->response->user->lastName;
		$this->user->profile->photoURL  	= @ $data->response->user->photo;
		$this->user->profile->profileURL    = @ "https://www.foursquare.com/user/" . $data->response->user->id;
		$this->user->profile->gender        = @ $data->response->user->gender;
		$this->user->profile->city          = @ $data->response->user->homeCity;
		$this->user->profile->email         = @ $data->response->user->contact->email;

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
