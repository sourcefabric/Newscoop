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
 * @copyright   Copyright (c) 2009, 2011 hybridAuth Dev Team.
 * @license     http://hybridauth.sourceforge.net/licenses.html under MIT and GPL
 * @link        http://hybridauth.sourceforge.net 
 */

// ------------------------------------------------------------------------

/**
 * Hybrid_Providers_Yahoo class 
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.1.0 
 * @link       http://hybridauth.sourceforge.net/userguide/IDProvider_info_Yahoo.html
 * @link       http://openid.net/developers/
 * @see        Hybrid_Provider_Model
 * @see        Hybrid_Provider_Adapter
 */
 
## http://itickr.com/?p=309
	// Main Links
	// OpenID Endpoint 	https://www.Yahoo.com/webapps/auth/server
	// OpenID Identifier 	https://www.Yahoo.com/webapps/auth/server
	// This should return the XRDS that can be used to discover the end point)
	// Docs Link 	https://www.x.com/community/ppx/xspaces/identity
	// Submit RP for whitelisting 	https://www.x.com/create-appvetting-app!input.jsp

class Hybrid_Providers_Yahoo extends Hybrid_Provider_Model
{
   /**
	* the IDp api client (optional). Not used on OpenID adapter
	*/
	var $api              = NULL;
	var $openidIdentifier = "https://www.yahoo.com/";
	var $openidParams     = NULL;
	var $openidConsumer   = NULL;
	var $openidStorage    = NULL;

	// --------------------------------------------------------------------

   /**
	* IDp wrappers initializer 
	*/
	function initialize() 
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::initialize(),  params:", $this->params );

		GLOBAL $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES;
		GLOBAL $GLOBAL_HYBRID_AUTH_STORAGE_TYPE;
		GLOBAL $GLOBAL_HYBRID_AUTH_STORAGE_PATH;
 
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Auth/OpenID/Consumer.php";
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Auth/OpenID/FileStore.php";
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Auth/OpenID/SReg.php";
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Auth/OpenID/PAPE.php";
		require_once $GLOBAL_HYBRID_AUTH_PATH_LIBRARIES . "Auth/OpenID/AX.php";

		# if HYBRID_AUTH_HYBRID_STORAGE_TYPE == File, we use file storage for OpenID library by JanRain,  
		# otherwise, fuck off
		if( $GLOBAL_HYBRID_AUTH_STORAGE_TYPE == "File" )
		{
			$this->openidStorage = new Auth_OpenID_FileStore( $GLOBAL_HYBRID_AUTH_STORAGE_PATH );
		}
	}

	// --------------------------------------------------------------------

   /**
	* begin login step
	* 
	* build an normalized Yahoo OpenID url to autentify the user with selected openid_identifier
	*/
	function loginBegin()
	{
		GLOBAL $GLOBAL_HYBRID_AUTH_URL_BASE; 

		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginBegin()" );

		$this->openidConsumer   = new Auth_OpenID_Consumer( $this->openidStorage );

		if ( ! $this->openidIdentifier )
		{
			throw new Exception( "Authentification failed! Missing reuqired parameter [openid_identifier] for Yahoo" );
		}

		// Begin the OpenID authentication process.
		$auth_request = $this->openidConsumer->begin( $this->openidIdentifier );

		// No auth request means we can't begin OpenID.
		if ( ! $auth_request )
		{
			throw new Exception( "Authentication failed! Given OpenID Identifier [{$this->openidIdentifier}] is taking too long to respond. Maybe is not a valid OpenID." );
		}
		
		// Create ax request (Attribute Exchange)
		$ax_request = new Auth_OpenID_AX_FetchRequest;
		$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first', 1, TRUE, 'firstname')); 
		$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last', 1, TRUE, 'lastname'));   
		$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email', 1, TRUE, 'email'));
		$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://schema.openid.net/contact/fullname', 1, TRUE, 'fullname'));
		$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/birthDate', 1, TRUE, 'dateofbirth'));
		$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/postalCode/home', 1, TRUE, 'postalcode'));
		$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/country/home', 1, TRUE, 'country'));
		$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/city/home', 1, TRUE, 'city'));
		$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/pref/language', 1, TRUE, 'language')); 
		$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/phone/default', 1, TRUE, 'phone'));
		$auth_request->addExtension($ax_request);

		// Redirect the user to the OpenID server for authentication.
		// Store the token for this authentication so we can verify the
		// response.

		// For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
		// form to send a POST request to the server.
		if ( $auth_request->shouldSendRedirect() ) 
		{
			$redirect_url = $auth_request->redirectUrl
							(
								$GLOBAL_HYBRID_AUTH_URL_BASE, 
								Hybrid_Auth::storage()->get( "hauth_session.hauth_endpoint" ) 
							);

			// If the redirect URL can't be built, display an error message.
			if ( Auth_OpenID::isFailure( $redirect_url ) )
			{
				throw new Exception( "Authentication failed! Could not redirect to server: " . $redirect_url->message ); 
			} 
			else
			{
				Hybrid_Auth::redirect( $redirect_url );
			}
		}
		else 
		{
		    // Generate form markup and render it.
			$form_id   = 'openid_message';
			$form_html = $auth_request->htmlMarkup
						(
							$GLOBAL_HYBRID_AUTH_URL_BASE, 
							Hybrid_Auth::storage()->get( "hauth_session.hauth_endpoint" ) , 
							FALSE , 
							array(	'id' => $form_id	)
						);

			// Display an error if the form markup couldn't be generated;
			// otherwise, render the HTML.
			if ( Auth_OpenID::isFailure( $form_html ) )
			{
				throw new Exception( "Authentication failed! Could not redirect to server: " . $form_html->message ); 
			} 
			else 
			{
				print $form_html;

				exit( 0 );
			}
		} 
	}

	// --------------------------------------------------------------------

   /**
	* finish login step
	* 
	* fetch returned parameters by Yahoo OpenID IDp 
	*/
	function loginFinish()
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::loginFinish()" );

		if( ! isset( $_REQUEST['openid_ns'] ) )
		{
			throw new Exception( "Authentification failed! The Yahoo provider has returned an invalid response." );
		} 

		$result = Auth_OpenID::getQuery();

		$this->openidConsumer = new Auth_OpenID_Consumer( $this->openidStorage );
		$response = $this->openidConsumer->complete( Hybrid_Auth::storage()->get( "hauth_session.hauth_endpoint" ), $result );

		// check openid provider response
		if ( $response->status == Auth_OpenID_CANCEL ) 
		{
			throw new Exception( "Authentification failed! Sign-in was cancelled." );
	    }
		elseif( $response->status == Auth_OpenID_FAILURE )
		{
			throw new Exception( "Authentification failed! Yahoo verification failed: " . $response->message );
	    }

		// This means the authentication succeeded; extract the
		// identity URL and Simple Registration data (if it was returned).
		elseif( $response->status == Auth_OpenID_SUCCESS )
		{
			$ax_args = Auth_OpenID_AX_FetchResponse::fromSuccessResponse($response);

			if ( $ax_args )
			{
				# store user ID, and detect user profile from the returned data 
				# OpenID provide the user profile once, so we store it from now 
				$this->user->profile->profileURL  = $response->getDisplayIdentifier();

				$ax_args = $ax_args->data;

				if (isset($ax_args['http://axschema.org/namePerson/first'][0]))        $this->user->profile->firstName   = $ax_args['http://axschema.org/namePerson/first'][0]; 
				if (isset($ax_args['http://axschema.org/namePerson/last'][0]))         $this->user->profile->lastName    = $ax_args['http://axschema.org/namePerson/last'][0];  
				if (isset($ax_args['http://axschema.org/contact/email'][0]))           $this->user->profile->email       = $ax_args['http://axschema.org/contact/email'][0];   
				if (isset($ax_args['http://schema.openid.net/contact/fullname'][0]))   $this->user->profile->displayName = $ax_args['http://schema.openid.net/contact/fullname'][0];
				if (isset($ax_args['http://axschema.org/contact/postalCode/home'][0])) $this->user->profile->zip         = $ax_args['http://axschema.org/contact/postalCode/home'][0];
				if (isset($ax_args['http://axschema.org/contact/country/home'][0]))    $this->user->profile->country     = $ax_args['http://axschema.org/contact/country/home'][0];
				if (isset($ax_args['http://axschema.org/contact/city/home'][0]))       $this->user->profile->city        = $ax_args['http://axschema.org/contact/city/home'][0];
				if (isset($ax_args['http://axschema.org/pref/language'][0]))           $this->user->profile->language    = $ax_args['http://axschema.org/pref/language'][0]; 
				if (isset($ax_args['http://axschema.org/contact/phone/default'][0]))   $this->user->profile->phone       = $ax_args['http://axschema.org/contact/phone/default'][0]; 

				if (isset($ax_args['http://axschema.org/birthDate'][0])){
					list($birthday_month, $birthday_day, $birthday_year) = explode('/', $ax_args['http://axschema.org/birthDate'][0] );

					$this->user->profile->birthDay      = $birthday_day;
					$this->user->profile->birthMonth    = $birthday_month;
					$this->user->profile->birthYear     = $birthday_year;
				}

				$this->user->profile->gender = strtolower( $this->user->profile->gender );

				if( $this->user->profile->gender == "f" ){
					$this->user->profile->gender = "female";
				}

				if( $this->user->profile->gender == "m" ){
					$this->user->profile->gender = "male";
				}

				// for yahoo we set the email as UID
				$this->user->providerUID = $this->user->profile->email;
			}
			else
			{
				throw new Exception( "Authentification failed! User has accepted the authentfication, but the Yahoo provider [{$this->openidIdentifier}] has not sent the user data." );
			}

			Hybrid_Logger::info( "[{$this->providerId}]::getUserProfile(), set user data", $this->user );

			Hybrid_Auth::storage()->set( "hauth_session.user.data" , $this->user ); 

			Hybrid_Logger::info( "[{$this->providerId}]::loginFinish(), Set user to connected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 1 ); 
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

		// OpenID DO NOT support force logout

		return TRUE;
	}

	// --------------------------------------------------------------------

   /**
	* load the user profile from the IDp api client
	*
	* OpenID provide the user profile once 
	*/
	function getUserProfile()
	{

		Hybrid_Logger::info( "Enter [{$this->providerId}]::getUserProfile()" );

		# to access this area, the user have to be loggedin with an IDp and has a HybridAuth session
		if ( ! Hybrid_Auth::hasSession() )
		{
			throw new Exception( "HybridAuth can't access user profile data. The current user have to sign in with [{$this->providerId}] before any request!" );
		}

		Hybrid_Auth::addWarning( "Yahoo provide the user profile only once" );
	}

	// --------------------------------------------------------------------

   /**
	* load the current logged in user contacts list from the IDp api client 
	*
	* HybridAuth dont provide users contats on version 1.0.x
	*
	* OpenID don't provide the user contacts list anyway
	*/
	function getUserContacts()
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::getUserContacts()" );
	}
} 