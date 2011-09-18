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
 * Hybrid_Providers_Facebook class, wrapper for Facebook Connect  
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.3
 * @since      HybridAuth 1.0
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_Facebook.html
 * @link       http://apps.facebook.com/footprints/
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
class Hybrid_Providers_Facebook extends Hybrid_Provider_Model
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

		if( empty( $this->config["keys"]["APPLICATION_ID"] ) )
		{
			throw new Exception( "APPLICATION_ID of [{$this->providerId}] is set to NULL!" );
		}

		if( empty( $this->config["keys"]["CONSUMER_SECRET"] ) )
		{
			throw new Exception( "CONSUMER_SECRET of [{$this->providerId}] is set to NULL!" );
		}

		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Facebook/facebook.php"; 
		
		$fb_conf = 	ARRAY(
						'appId'  => $this->config["keys"]["APPLICATION_ID"]	, 
						'secret' => $this->config["keys"]["CONSUMER_SECRET"]
					);

		$this->api = new Facebook( $fb_conf );
		
		Hybrid_Logger::info( "[{$this->providerId}] wrapper initialized", $this->api );
	}

	// --------------------------------------------------------------------

   /**
	* begin login step
	* 
	* simply call Facebook::require_login(). 
	*/
	function loginBegin()
	{
		GLOBAL $GLOBAL_HYBRID_AUTH_URL_EP;

		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );

		$url = $this->api->getLoginUrl(array(
						'scope'        => 'email, user_about_me, user_birthday, user_hometown, user_website',
						'redirect_uri' => $GLOBAL_HYBRID_AUTH_URL_EP . ( strpos( $GLOBAL_HYBRID_AUTH_URL_EP, '?' ) ? '&' : '?' ) . "hauth.done=Facebook",
					));  

		Hybrid_Logger::info( "[{$this->providerId}]::loginBegin() redirect to url", $url );

		echo "<script type='text/javascript'>top.location.href = '$url';</script>";

		die();
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

		try
		{
			$this->api->getUser();
		}
		catch( Exception $e )
		{
			Hybrid_Auth::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString() ); 

			throw new Exception( "Error: " . $e->getMessage() );
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

		// Facebook support force logout
			// to do base_facebook.php - getLogoutUrl($params=array())

		return TRUE;
	} 

	// --------------------------------------------------------------------

   /**
	* load the user profile from the IDp api client
	*/
	function getUserProfile()
	{

		Hybrid_Logger::info( "Enter [{$this->providerId}]::getUserProfile()" );

		# to access this area, the user have to be loggedin with an IDp an has a HybridAuth session
		if ( ! Hybrid_Auth::hasSession() )
		{
			throw new Exception( "HybridAuth can't access user profile data. The current user have to sign in with [{$this->providerId}] before any request!" );
		}

		$data = $this->api->api('/me');

		Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), Get user data", $data );

		// if the provider identifier is not recived, we assume the auth has failed
		if ( ! isset( $data["id"] ) )
		{
			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), reSet user to deconnected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			throw new Exception( "User profile request failed! The user was able to authenticate with {$this->providerId}, but the provider has failed to return the user profile." );
		}

		$this->user->providerUID            = @ $data['id'];
		$this->user->profile->displayName   = @ $data['name'];
		$this->user->profile->firstName     = @ $data['first_name'];
		$this->user->profile->lastName     	= @ $data['last_name'];
		$this->user->profile->photoURL      = "https://graph.facebook.com/" . $this->user->providerUID . "/picture";
		$this->user->profile->profileURL 	= @ $data['link']; 
		$this->user->profile->webSiteURL 	= @ $data['website']; 
		$this->user->profile->gender     	= @ $data['gender'];
		$this->user->profile->description  	= @ $data['bio'];
		$this->user->profile->email      	= @ $data['email'];

		if( isset( $data['birthday'] ) ) {
			list($birthday_month, $birthday_day, $birthday_year) = @ explode('/', $data['birthday'] );

			$this->user->profile->birthDay      = $birthday_day;
			$this->user->profile->birthMonth    = $birthday_month;
			$this->user->profile->birthYear     = $birthday_year;
		} 

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
