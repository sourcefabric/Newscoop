<?php
/**
 * HybridAuth
 * 
 * An open source Web based Single-Sign-On PHP Library used to authentificates users with
 * major Web account providers and accessing social and data apis at Google, Facebook,
 * Yahoo!, MySpace, Live, Windows live ID, etc. 
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
 * Hybrid_Providers_Live class, wrapper for Live auth/api
 *
 * some code is based on Live OAuth PHP LIB by Abraham Williams
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.1.0 
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_Live.html
 * @link       http://apiwiki.Live.com/Sign-in-with-Live
 * @link       http://abrah.am
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
class Hybrid_Providers_Live extends Hybrid_Provider_Model
{
   /**
	* the IDp api client (optional)
	*/
	var $api = NULL; 
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

		if( empty( $this->config["keys"]["CONSUMER_ID"] ) )
		{
			throw new Exception( "Your {$this->providerId} CONSUMER_ID is set to NULL! Please check HybridAuth configuration file." );
		}

		if( empty( $this->config["keys"]["CONSUMER_SECRET"] ) )
		{
			throw new Exception( "Your {$this->providerId} CONSUMER_SECRET is set to NULL! Please check your HybridAuth configuration file." );
		}

		// Application Specific Globals
		define( 'WRAP_CLIENT_ID'       , $this->config["keys"]["CONSUMER_ID"]);
		define( 'WRAP_CLIENT_SECRET'   , $this->config["keys"]["CONSUMER_SECRET"] ); 
		define( 'WRAP_CALLBACK'        , $GLOBAL_HYBRID_AUTH_URL_EP . "?hauth.done=Live" );
		define( 'WRAP_CHANNEL_URL'     , $GLOBAL_HYBRID_AUTH_URL_EP . "?get=windows_live_channel" );

		// Live URLs required for making requests.
		define('WRAP_CONSENT_URL'      , 'https://consent.live.com/Connect.aspx');
		define('WRAP_ACCESS_URL'       , 'https://consent.live.com/AccessToken.aspx');
		define('WRAP_REFRESH_URL'      , 'https://consent.live.com/RefreshToken.aspx');

		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "WindowsLive/OAuthWrapHandler.php";  

		$this->api = new OAuthWrapHandler();
	}

	// --------------------------------------------------------------------

   /**
	* begin login step 
	*/
	function loginBegin()
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );
		
		Hybrid_Auth::storage()->delete( "hauth_session.live.access_token" ); 

		$this->api->ExpireCookies();

		$this->redirect_uri = WRAP_CONSENT_URL . "?wrap_client_id=" . WRAP_CLIENT_ID . "&wrap_callback=" . urlencode( WRAP_CALLBACK ) . "&wrap_scope=WL_Profiles.View"; 

		Hybrid_Auth::redirect( $this->redirect_uri ); 
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

		Hybrid_Logger::debug( "[{$this->providerId}]::loginFinish(), received http request", $_REQUEST );

		$response = $this->api->ProcessRequest();

		$this->user->providerUID = @ (string) $response['c_uid'];

		if ( ! isset( $response['c_uid'] ) || ! isset( $response['c_accessToken'] ) )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Token." );
		}

		Hybrid_Logger::info( "[{$this->providerId}]::loginFinish(), Set user to connected" );
		Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 1 );

		# store access token
		Hybrid_Auth::storage()->set( "hauth_session.live.access_token",  $response['c_accessToken'] ); 

		# store the user id. 
		$this->user->providerUID = (string) $response['c_uid'];

		# grab user profile
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

		// Dunno if Live support force logout

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

		$access_token = Hybrid_Auth::storage()->get( "hauth_session.live.access_token" ); 
		$info_url     = 'http://apis.live.net/V4.1/cid-'. $this->user->providerUID .'/Profiles/1-' . $this->user->providerUID ;
		
		$response    = $this->api->GET($info_url, false, $access_token );
		$response    = @ json_decode( $response );

		Hybrid_Logger::debug( "[{$this->providerId}]::getUserProfile(), received response", $response );

		if ( ! is_object( $response ) )
		{
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			throw new Exception( "User profile request failed! {$this->providerId} returned an invalid response." );
		}

		$this->user->profile->firstName   = @ (string) $response->FirstName; 
		$this->user->profile->lastName    = @ (string) $response->LastName; 
		$this->user->profile->profileURL  = @ (string) $response->UxLink; 
		$this->user->profile->gender      = @ (string) $response->Gender; 
		$this->user->profile->email       = @ (string) $response->Emails[0]->Address; 

		$this->user->profile->displayName = $this->user->profile->firstName . " " . $this->user->profile->lastName; 

		if( $this->user->profile->gender == 1 ){
			$this->user->profile->gender = "female";
		}
		elseif( $this->user->profile->gender == 2 ){
			$this->user->profile->gender = "male";
		}
		else{
			$this->user->profile->gender = "";
		}

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
}
