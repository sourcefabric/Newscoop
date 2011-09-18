<?php
/**
 * HybridAuth
 * 
 * An open source Web based Single-Sign-On PHP Library used to authentificates users with
 * major Web account providers and accessing social and data apis at Google, Facebook,
 * Yahoo!, MySpace, Gowalla, Windows live ID, etc. 
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
 * Hybrid_Providers_Gowalla class, wrapper for Gowalla auth/api
 *
 * some code is based on Gowalla OAuth PHP LIB by Abraham Williams
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.1.0 
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_Gowalla.html
 * @link       http://apiwiki.Gowalla.com/Sign-in-with-Gowalla
 * @link       http://abrah.am
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
class Hybrid_Providers_Gowalla extends Hybrid_Provider_Model
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

		if( empty( $this->config["keys"]["CONSUMER_KEY"] ) )
		{
			throw new Exception( "Your {$this->providerId} CONSUMER_KEY is set to NULL! Please check HybridAuth configuration file." );
		}

		if( empty( $this->config["keys"]["CONSUMER_SECRET"] ) )
		{
			throw new Exception( "Your {$this->providerId} CONSUMER_SECRET is set to NULL! Please check your HybridAuth configuration file." );
		}

		$this->redirect_uri = $GLOBAL_HYBRID_AUTH_URL_EP . ( strpos( $GLOBAL_HYBRID_AUTH_URL_EP, '?' ) ? '&' : '?' ) . "hauth.done=Gowalla&";

		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Gowalla/Gowalla.php";  

		$this->api = new Gowalla
					(
						$this->config["keys"]["CONSUMER_KEY"], 
						$this->config["keys"]["CONSUMER_SECRET"] , 
						$this->redirect_uri 
					);
	}

	// --------------------------------------------------------------------

   /**
	* begin login step 
	*/
	function loginBegin()
	{
		GLOBAL $GLOBAL_HYBRID_AUTH_URL_EP;
	
		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );
		
		Hybrid_Auth::storage()->delete( "hauth_session.gowalla.access_token" );

		// authenticate app
		$this->api->authenticate();
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

		$response = $this->api->requestToken( $code );

		Hybrid_Logger::debug( "[{$this->providerId}]::loginFinish(), received response", $response );

		if ( ! $response )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Token." );
		}

		Hybrid_Auth::storage()->set( "hauth_session.gowalla.access_token" , $response['access_token'] );   

		Hybrid_Logger::info( "[{$this->providerId}]::loginFinish(), Set user to connected" );
		Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 1 );

		$this->api = new Gowalla
					(
						$this->config["keys"]["CONSUMER_KEY"], 
						$this->config["keys"]["CONSUMER_SECRET"] ,  
						$this->redirect_uri , 
						Hybrid_Auth::storage()->get( "hauth_session.gowalla.access_token" )
					);

		Hybrid_Logger::info( "[{$this->providerId}]::loginFinish(), [{$this->providerId}] wrapper initialized", $this->api );

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

		// Dunno if Gowalla support force logout

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

		$response = $this->api->getMe();

		Hybrid_Logger::debug( "[{$this->providerId}]::getUserProfile(), received response", $response );

		if ( ! $response )
		{
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			throw new Exception( "User profile request failed! {$this->providerId} returned an invalid response." );
		}

		$this->user->providerUID         	= @ (string) $response["username"]; 
		$this->user->profile->displayName  	= @ (string) $response["username"]; 
		$this->user->profile->firstName  	= @ (string) $response["first_name"]; 
		$this->user->profile->lastName  	= @ (string) $response["last_name"]; 

		if( isset( $response["url"] ) ){
			$this->user->profile->profileURL = @ "http://gowalla.com" . ( (string) $response["url"] ); 
		}

		$this->user->profile->webSiteURL 	= @ (string) $response["website"]; 
		$this->user->profile->photoURL   	= @ (string) $response["image_url"]; 

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
