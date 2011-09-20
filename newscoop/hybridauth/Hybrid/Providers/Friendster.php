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
 * Hybrid_Providers_Friendster class, wrapper for Friendster auth/api
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.1
 * @since      HybridAuth 1.0.1 
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_Friendster.html
 * @link       http://www.friendster.com/developer
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
class Hybrid_Providers_Friendster extends Hybrid_Provider_Model
{
   /**
	* the IDp api client (optional)
	*/
	var $api = NULL;

   /**
	* Friendster login url 
	*/
	var $authUrlBase = "http://www.friendster.com/widget_login.php?api_key=";

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

		Hybrid_Logger::info( "Enter [{$this->providerId}]::initialize()" );

		GLOBAL $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES;

		if( empty( $this->config["keys"]["CONSUMER_KEY"] ) )
		{
			throw new Exception( "CONSUMER_KEY of [{$this->providerId}] is set to NULL!" );
		}

		if( empty( $this->config["keys"]["CONSUMER_SECRET"] ) )
		{
			throw new Exception( "CONSUMER_SECRET of [{$this->providerId}] is set to NULL!" );
		}

		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Friendster/FriendsterAPI.php";

		// If we have a session_key, set it
		if 
		(
			Hybrid_Auth::storage()->get( "hauth_session.friendster.session_key" )
		)
		{
			$this->api = new FriendsterAPI
						( 
							$this->config["keys"]["CONSUMER_KEY"], 
							$this->config["keys"]["CONSUMER_SECRET"],
							Hybrid_Auth::storage()->get( "hauth_session.friendster.session_key" )
						);
		}
		else
		{
			$this->api = new FriendsterAPI( $this->config["keys"]["CONSUMER_KEY"], $this->config["keys"]["CONSUMER_SECRET"] );
		}
	}

	// --------------------------------------------------------------------

   /**
	* begin login step
	* 
	* All what we need to know is on Access from an External Web Application section of developer guide
	* see http://www.friendster.com/developer
	* 
	*	// Access from an External Web Application
	*	// =======================================
	*	// External Web applications can access the Friendster APIs after authentication through the Login URL.
	*	// A login prompt lets the user enter his/her username and password and then calls the Callback URL.
	*	// The Login URLs for production are as follows:
	*	    // http://www.friendster.com/widget_login.php?api_key=<API_KEY>&next=<ENCODED_ARGS>
	*		// Example:
	*			// For instance the following application passes its own internal user ID to the login request:
	*			// http://www.friendster.com/widget_login.php?api_key=2e37638f335f0545c3719d34f4d20ed0 
	*			// Assuming the callback URL is http://mydomain/apps/1444, it would be called as follows:
	*			// http://mydomain/apps/1444?
	*			    // api_key=2e37638f335f0545c3719d34f4d20ed0&
	*			    // src=login&
	*			    // auth_token=846d79676186569.74429552& 
	*			    // lang=en-US&
	*			    // nonce=326233766.3425&
	*			    // sig=012345678901234567890123456789012
	*
	*/
	function loginBegin()
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );

		Hybrid_Auth::storage()->delete( "hauth_session.friendster.session_key" );
		Hybrid_Auth::storage()->delete( "hauth_session.friendster.auth_token" );
		Hybrid_Auth::storage()->delete( "hauth_session.friendster.session_key" );

		$auth_url = $this->authUrlBase . $this->config["keys"]["CONSUMER_KEY"];

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

		// try to validate the request was really constructed and sent by Friendster 
		try{ 
			FriendsterAPI::validateSignature( $this->config["keys"]["CONSUMER_SECRET"] );
		}
		catch( FriendsterAPIException $e )
		{
			throw new Exception( "Authentification failed! {$this->providerId} has returned an invalide response. " . $e->getMessage() ); 
		}

		/* use this if you created a class object with $session_key == null */
			// public function session($token = null) {
		# ..., okey, will make use of it, thanks

		$auth_token = @ $_REQUEST['auth_token'];

		if ( ! $auth_token )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Authentication token." );
		}

		$session_key = $this->api->session( $auth_token );

		if ( ! $session_key )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Session key." );
		}

		Hybrid_Logger::info( "[{$this->providerId}]::loginFinish(), Set user to connected" );
		Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 1 );

		Hybrid_Auth::storage()->set( "hauth_session.friendster.auth_token" , $auth_token  ); 
		Hybrid_Auth::storage()->set( "hauth_session.friendster.session_key", $session_key ); 

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

		# to access this area, the user have to be loggedin with and IDp an has a HybridAuth session
		if ( ! Hybrid_Auth::hasSession() )
		{
			throw new Exception( "HybridAuth can't access user profile data. The current user have to sign in with [{$this->providerId}] before any request!" );
		}

		// Access user info from friendster API
			// from http://www.friendster.com/developer
			// Get User Information
				// Resource URL: http://api.friendster.com/v1/user/<UID>
				// Resource Methods: GET (Retrieval of user information)
				// Resource Description: API to get user information on one or more users. If no user ID is specified, 
				// information about current logged in user will be returned.
				//                   =======================================

		# ..., cool, anyway we dont know the user id 
		$data = $this->api->users( NULL, NULL, FriendsterAPIResponseType::JSON ); 

		if ( ! isset( $data["user"] ) )
		{
			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), reSet user to deconnected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			throw new Exception( "Request failed! {$this->providerId} returned an invalide response." );
		}

		$data = $data["user"]; 

		Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), Get user data", $data );

		$this->user->providerUID            = $data["uid"];

		$this->user->profile->displayName   = @ $data['first_name'] . " " . $data['last_name'];
		$this->user->profile->description  	= @ $data['about_me'];
		$this->user->profile->gender     	= @ strtolower( $data['gender'] );
		$this->user->profile->firstName     = @ $data['first_name'];
		$this->user->profile->lastName     	= @ $data['last_name'];

		$this->user->profile->photoURL   	= @ $data['primary_photo_url'];
		$this->user->profile->profileURL 	= @ $data['url'];  

		$this->user->profile->birthDay 		= @ $data['birthday']['day'];  
		$this->user->profile->birthMonth 	= @ $data['birthday']['month'];  
		$this->user->profile->birthYear 	= @ $data['birthday']['year'];  

		$this->user->profile->country 		= @ $data['location']['country'];  
		$this->user->profile->region 		= @ $data['location']['state'];  
		$this->user->profile->city 			= @ $data['location']['city'];  
		$this->user->profile->zip 			= @ $data['location']['zip'];  

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
