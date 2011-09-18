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
 * Hybrid_Providers_Google class, wrapper for Google user accounts and hosted apps
 *
 * some code is based on gdata-samples project 
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.2
 * @since      HybridAuth 1.0.1 
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_Google.html
 * @link       http://googlecodesamples.com/
 * @link       http://code.google.com/p/gdata-samples/source/browse/#svn/trunk/hybrid
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
class Hybrid_Providers_Google extends Hybrid_Provider_Model
{
   /**
	* the IDp api client (optional)
	*/
	var $api               = NULL;
	var $service           = "Users"; // can be "Users" (google user accounts) or "Apps" (hosted apps)
	var $googleIdentifiers = Array
							(
								"Users" => "https://www.google.com/accounts/o8/id", 
								"Apps" 	=> "https://www.google.com/accounts/o8/site-xrds?hd={hosted_domain_name}"
							);
	var $openidIdentifier  = NULL;
	var $hostedDomain      = NULL; // hosted_domain_name for Google "Apps" (hosted) accounts
	var $openidParams      = NULL;
	var $oauthScopes       = NULL;

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
		GLOBAL $GLOBAL_HYBRID_AUTH_URL_BASE; 
		GLOBAL $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES;

		// # check Google's key and secret
		if( empty( $this->config["keys"]["CONSUMER_KEY"] ) )
		{
			throw new Exception( "CONSUMER_KEY of [{$this->providerId}] is set to NULL!" );
		}

		if( empty( $this->config["keys"]["CONSUMER_SECRET"] ) )
		{
			throw new Exception( "CONSUMER_SECRET of [{$this->providerId}] is set to NULL!" );
		}

		if( ! isset(  $this->params["google_service"] ) )
		{
			$this->params["google_service"] = "Users";
		}

		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Auth/OpenID/Consumer.php";
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Auth/OpenID/FileStore.php";
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Auth/OpenID/SReg.php";
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Auth/OpenID/PAPE.php"; 
 
		$this->openidParams = array
		(
			"openid.claimed_id" 		=> "http://specs.openid.net/auth/2.0/identifier_select",
			"openid.ext1.mode" 			=> "fetch_request",
			"openid.ext1.required" 		=> "email,first,last,country,lang",
			"openid.ext1.type.country" 	=> "http://axschema.org/contact/country/home",
			"openid.ext1.type.email" 	=> "http://axschema.org/contact/email",
			"openid.ext1.type.first" 	=> "http://axschema.org/namePerson/first",
			"openid.ext1.type.lang" 	=> "http://axschema.org/pref/language",
			"openid.ext1.type.last" 	=> "http://axschema.org/namePerson/last",
			"openid.identity" 			=> "http://specs.openid.net/auth/2.0/identifier_select",
			"openid.mode" 				=> "checkid_setup",
			"openid.ns" 				=> "http://specs.openid.net/auth/2.0",
			"openid.ns.ext1" 			=> "http://openid.net/srv/ax/1.0",
			"openid.ns.oauth" 			=> "http://specs.openid.net/extensions/oauth/1.0",
			"openid.ns.ui" 				=> "http://specs.openid.net/extensions/ui/1.0", 
			"openid.oauth.consumer" 	=> $this->config["keys"]["CONSUMER_KEY"], 
			"openid.return_to" 			=> Hybrid_Auth::storage()->get( "hauth_session.hauth_endpoint" )
		); 
		
	}

	// --------------------------------------------------------------------

   /**
	* begin login step
	* 
	* google_service must be "User" for Google user accounts service or "Apps" for Google hosted Apps
	* if chosen google_service eq "Apps", google_hosted_domain parameter will be required
	*
	* build an normalized OpenID url to autentify the user with selected google_service openid identifier
	*/
	function loginBegin( )
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );

		# google_service must be "User" for Google user accounts service or "Apps" for Google hosted Apps
		if( $this->params["google_service"] == "Users" )
		{
			$this->openidIdentifier = $this->googleIdentifiers["Users"];
		}
		elseif( $this->params["google_service"] == "Apps" )
		{
			# if chosen google_service eq "Apps", google_hosted_domain parameter will be required
			if( isset( $this->params["google_hosted_domain"] ) && ! empty( $this->params["google_hosted_domain"] ) )
			{
				$this->hostedDomain = $this->params["google_hosted_domain"];
			}
			else
			{
				throw new Exception( "Authentification failed! Missing reuqired parameter [google_hosted_domain] for hosted Apps service!" );
			}

			$this->openidIdentifier = str_replace( "{hosted_domain_name}", $this->hostedDomain, $this->googleIdentifiers["Apps"] );
		}
		else
		{
			throw new Exception( "Authentification failed! Only Google [Users] and [Apps] accounts services are supported!" );
		}

		$this->service = $this->params["google_service"];

		# build an normalized OpenID url to autentify the user with selected google_service openid identifier
		$fetcher = Auth_Yadis_Yadis::getHTTPFetcher();
		list( $normalized_identifier, $endpoints ) = Auth_OpenID_discover( $this->openidIdentifier, $fetcher );

		if ( ! $endpoints )
		{
			throw new Exception( "Authentification failed! No OpenID endpoint found for [{$this->openidIdentifier}]." );
		}

		$auth_url = $endpoints[0]->server_url . '?' . trim( http_build_query( $this->openidParams ) ) ;
		
		# redirect the user to the selected google openid identifier authentification page, and exit
		
		Hybrid_Auth::redirect( $auth_url );
	}

	// --------------------------------------------------------------------

   /**
	* finish login step
	* 
	* fetch returned parameters by selected google openid identifier $this->googleIdentifiers[ $this->params["google_service"] ]
	* then Query Google's Portable Contacts API to get user contacts list
	*/
	function loginFinish( )
	{
		# if user don't garant acess of their data to your site, halt with an Exception
		if( isset( $_REQUEST['openid_mode'] ) && $_REQUEST['openid_mode'] == 'cancel' )
		{
			throw new Exception( "Authentification failed! Sign-in was cancelled." );
		} 

		# else, if user garant acess
		if( isset( $_REQUEST['openid_mode'] ) && $_REQUEST['openid_mode'] == 'id_res' )
		{
			Hybrid_Logger::info( "[{$this->providerId}]::loginFinish(), Set user to connected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 1 ); 

			# store user ID, 
			# in Google case, uid can be an unique url "openid_identity" or his email, so we choose "openid_identity"
			$this->user->providerUID          = @ $_REQUEST["openid_identity"];  

			# detect user profile from the returned data 
			// $this->user->profile->profileURL  = @ $_REQUEST["openid_identity"];
			$this->user->profile->firstName   = @ $_REQUEST['openid_ext1_value_first'];
			$this->user->profile->lastName    = @ $_REQUEST['openid_ext1_value_last'];
			$this->user->profile->displayName = $this->user->profile->firstName . " " . $this->user->profile->lastName;
			$this->user->profile->email       = @ $_REQUEST['openid_ext1_value_email'];
			$this->user->profile->language    = @ $_REQUEST['openid_ext1_value_lang'];
			$this->user->profile->country     = @ $_REQUEST['openid_ext1_value_country'];

			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), set user data", $this->user );

			Hybrid_Auth::storage()->set( "hauth_session.user.data" , $this->user );
		}
		else
		{
			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), reSet user to deconnected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			throw new Exception( "Authentification failed! For some reason, {$this->providerId} has returned an invalid response. Giving up." );
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
		# to access this area, the user have to be loggedin with an IDp and has a HybridAuth session
		if ( ! Hybrid_Auth::hasSession() )
		{
			throw new Exception( "HybridAuth can't access user profile data. The current user have to sign in with [{$this->providerId}] before any request!" );
		}

		return NULL; 
	}

	// --------------------------------------------------------------------

   /**
	* load the current logged in user contacts list from the IDp api client 
	*
	* HybridAuth dont provide users contats on version 1.0.x 
	*/
	function getUserContacts()
	{
	}
}
