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
 * Hybrid_Providers_LinkedIn class, wrapper for LinkedIn auth/api
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      hybridAuth 1.1
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_LinkedIn.html
 * @link       http://en.netlog.com/go/developer/documentation/
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
class Hybrid_Providers_LinkedIn extends Hybrid_Provider_Model
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

		if( empty( $this->config["keys"]["CONSUMER_KEY"] ) )
		{
			throw new Exception( "CONSUMER_KEY of [{$this->providerId}] is set to NULL!" );
		}

		if( empty( $this->config["keys"]["CONSUMER_SECRET"] ) )
		{
			throw new Exception( "CONSUMER_SECRET of [{$this->providerId}] is set to NULL!" );
		}

		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "LinkedIn/OAuth.php"; 
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "LinkedIn/LinkedIn.php"; 

		$API_CONFIG = array(
			'appKey'       => $this->config["keys"]["CONSUMER_KEY"],
			'appSecret'    => $this->config["keys"]["CONSUMER_SECRET"],
			'callbackUrl'  => $GLOBAL_HYBRID_AUTH_URL_EP . ( strpos( $GLOBAL_HYBRID_AUTH_URL_EP, '?' ) ? '&' : '?' ) . "hauth.done=LinkedIn" 
		);

		$this->api = new LinkedIn( $API_CONFIG ); 
	}

	// --------------------------------------------------------------------

   /**
	* begin login step 
	*/
	function loginBegin()
	{
		GLOBAL $GLOBAL_HYBRID_AUTH_URL_EP;
	
		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );

		Hybrid_Auth::storage()->delete( "hauth_session.linkedin.oauth_token" );
		Hybrid_Auth::storage()->delete( "hauth_session.linkedin.oauth_token_secret" );
		Hybrid_Auth::storage()->delete( "hauth_session.linkedin.oauth_callback_confirmed" );
		Hybrid_Auth::storage()->delete( "hauth_session.linkedin.xoauth_request_auth_url" );
		Hybrid_Auth::storage()->delete( "hauth_session.linkedin.oauth_expires_in" );
		
        // send a request for a LinkedIn access token
        $response = $this->api->retrieveTokenRequest();

		Hybrid_Logger::debug( "[{$this->providerId}]::loginBegin(), received response", $response );
 
        if( isset( $response['success'] ) && $response['success'] === TRUE ) 
		{
			Hybrid_Auth::storage()->set( "hauth_session.linkedin.oauth_token"               , $response['linkedin']['oauth_token'] ); 
			Hybrid_Auth::storage()->set( "hauth_session.linkedin.oauth_token_secret"        , $response['linkedin']['oauth_token_secret'] ); 
			Hybrid_Auth::storage()->set( "hauth_session.linkedin.oauth_callback_confirmed"  , $response['linkedin']['oauth_callback_confirmed'] ); 
			Hybrid_Auth::storage()->set( "hauth_session.linkedin.xoauth_request_auth_url"   , $response['linkedin']['xoauth_request_auth_url'] ); 
			Hybrid_Auth::storage()->set( "hauth_session.linkedin.oauth_expires_in"          , $response['linkedin']['oauth_expires_in'] ); 

			$authorize_link = LINKEDIN::_URL_AUTH . $response['linkedin']['oauth_token'];

			# redirect user to LinkedIn authorisation web page
			Hybrid_Auth::redirect( $authorize_link );
        } 
		else 
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Token." );
        }
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

		$oauth_token    = @ $_REQUEST['oauth_token'];
		$oauth_verifier = @ $_REQUEST['oauth_verifier'];

		if ( ! $oauth_verifier )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Token." );
		}

		$response = $this->api->retrieveTokenAccess( 
													$oauth_token, 
													Hybrid_Auth::storage()->get( "hauth_session.linkedin.oauth_token_secret" ), 
													$oauth_verifier
												);

		Hybrid_Logger::debug( "[{$this->providerId}]::loginFinish(), received response", $response );

        if( isset( $response['success'] ) && $response['success'] === TRUE ) 
		{
			Hybrid_Logger::info( "[{$this->providerId}]::loginFinish(), Set user to connected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 1 );
        }
		else 
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Token." );
        }

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

		// add / remove watever u want
		// http://developer.linkedin.com/docs/DOC-1061
		$response = $this->api->profile('~:(id,first-name,last-name,public-profile-url,picture-url,date-of-birth,phone-numbers,summary)');

		if( isset( $response['success'] ) && $response['success'] === TRUE ) 
		{
			$data = new SimpleXMLElement($response['linkedin']); 

			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), Get user data", $data );

			if ( ! is_object( $data ) )
			{
				Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), reSet user to deconnected" );
				Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
				Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

				throw new Exception( "User profile request failed! {$this->providerId} returned an invalide xml data." );
			}  

			$this->user->providerUID         	= @ (string) $data->{'id'};
			$this->user->profile->firstName  	= @ (string) $data->{'first-name'};
			$this->user->profile->lastName  	= @ (string) $data->{'last-name'}; 
			$this->user->profile->displayName  	= @ $this->user->profile->firstName . " " . $this->user->profile->lastName;

			$this->user->profile->photoURL  	= @ (string) $data->{'picture-url'}; 
			$this->user->profile->profileURL    = @ (string) $data->{'public-profile-url'}; 
			$this->user->profile->description   = @ (string) $data->{'summary'};  

			$this->user->profile->phone         = @ (string) $data->{'phone-numbers'}->{'phone-number'}->{'phone-number'};  

			if( $data->{'date-of-birth'} ) { 
				$this->user->profile->birthDay      = @ (string) $data->{'date-of-birth'}->day;  
				$this->user->profile->birthMonth    = @ (string) $data->{'date-of-birth'}->month;  
				$this->user->profile->birthYear     = @ (string) $data->{'date-of-birth'}->year;  
			} 

			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), set user data", $this->user );

			Hybrid_Auth::storage()->set( "hauth_session.user.data", $this->user );
		} 
		else {
			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), reSet user to deconnected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			throw new Exception( "User profile request failed! {$this->providerId} returned an invalide response." );
		}

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
