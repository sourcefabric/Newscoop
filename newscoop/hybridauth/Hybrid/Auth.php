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
 * The main include file for Hybrid_Auth package
 *
 * The Hybrid_Auth class provides methods for creating an authentication 
 * system using PHP and major account providers ( OpenID, AOL, Facebook, 
 * Google, Yahoo, Twitter, MySpace, Live ID, etc. ) 
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.0.1 
 */
class Hybrid_Auth 
{
   /**
	* Hybrid storage system accessor
	*
	* Users sessions are stored using Hybrid storage system and can be acessed directly by
	* Hybrid_Auth::storage()->get($key) to retrieves the data for the given key, or calling
	* Hybrid_Auth::storage()->set($key, $value) to store the key => $value set.
	* 
	* eg.: 
	*   Current user id session
	*   Hybrid_Auth::storage()->get( "hauth_session.hauth_token"		)
	*   Hybrid_Auth::storage()->get( "hauth_session.hauth_time"         )
	*
	*   User state, profile, contacts list, etecta
	*	Hybrid_Auth::storage()->get( "hauth_session.is_logged_in"       )
	*	Hybrid_Auth::storage()->get( "hauth_session.user.data"          )
	*
	*   if shit happen, we store errors too
	*	Hybrid_Auth::storage()->get( "hauth_session.error.satus"        )
	*	Hybrid_Auth::storage()->get( "hauth_session.error.message"      )
	*	Hybrid_Auth::storage()->get( "hauth_session.error.code"         )
	*	Hybrid_Auth::storage()->get( "hauth_session.error.trace"        )
	*
	*   some extra identity provider (IPD or IDProvider) params
	*	Hybrid_Auth::storage()->get( "hauth_session.id_provider_id"     )
	*	Hybrid_Auth::storage()->get( "hauth_session.id_provider_params" )
	*	Hybrid_Auth::storage()->get( "hauth_session.hauth_endpoint"     )
	*	Hybrid_Auth::storage()->get( "hauth_session.hauth_return_to"    )
	* 
	* Hybrid storage system based on from osapiStorage (opensocial php client) 
	* almost 'AS IS', osapiStorage was written by Chris Chabot under apache license.
	* osapiStorage implement three type of backend to store user session : Memcahe, Files
	* and Apc, and developers are free to implement their own session store implementations 
	* using Hybrid_Storage model
	*
	* @return Hybrid_Storage
	* @see    Hybrid_Storage 
	*/
	public static function storage( )
	{
		GLOBAL $GLOBAL_HYBRID_AUTH_STORAGE_INSTANCE;

		return $GLOBAL_HYBRID_AUTH_STORAGE_INSTANCE;
	}

	// --------------------------------------------------------------------

   /**
	* try to start the Hybrid storage system
	*
	* @return boolean TRUE if storage obj created
	* @throws Exception if 
	* @see    Hybrid_Storage 
	*/
	public static function startStorage( )
	{
		Hybrid_Logger::info( "Enter Hybrid_Auth::startStorage()" );

		GLOBAL $GLOBAL_HYBRID_AUTH_STORAGE_TYPE;
		GLOBAL $GLOBAL_HYBRID_AUTH_STORAGE_PATH;
		GLOBAL $GLOBAL_HYBRID_AUTH_STORAGE_HOST;
		GLOBAL $GLOBAL_HYBRID_AUTH_STORAGE_PASS;
		GLOBAL $GLOBAL_HYBRID_AUTH_STORAGE_INSTANCE;

		$hauthStore = "Hybrid_Storage_{$GLOBAL_HYBRID_AUTH_STORAGE_TYPE}";

		switch( $GLOBAL_HYBRID_AUTH_STORAGE_TYPE )
		{
			case "Session"	:	$GLOBAL_HYBRID_AUTH_STORAGE_INSTANCE = new $hauthStore();
								break;
			case "File"		:	$GLOBAL_HYBRID_AUTH_STORAGE_INSTANCE = new $hauthStore( $GLOBAL_HYBRID_AUTH_STORAGE_PATH );
								break;
			case "Apc"		:
			case "Memcache"	:	$GLOBAL_HYBRID_AUTH_STORAGE_INSTANCE = new $hauthStore
																		( 
																			$GLOBAL_HYBRID_AUTH_STORAGE_HOST,
																			$GLOBAL_HYBRID_AUTH_STORAGE_PASS
																		); 
								break;
			default			: 	throw new Exception( "Unknwon Storage type [{$GLOBAL_HYBRID_AUTH_STORAGE_TYPE}], check " .
								"\$GLOBAL_HYBRID_AUTH_STORAGE_TYPE value in HybridAuth configuration file!" );
		}

		$GLOBAL_HYBRID_AUTH_STORAGE_INSTANCE->storageKey = session_id();

		Hybrid_Logger::info( "Hybrid_Auth::startStorage() storageKey", $GLOBAL_HYBRID_AUTH_STORAGE_INSTANCE->storageKey );

		return TRUE;
	}

	// --------------------------------------------------------------------

   /**
	* expire some saved vars for the current connected user
	*
	* @see    Hybrid_Storage 
	*/
	public static function expireStorage()
	{
		Hybrid_Logger::info( "Enter Hybrid_Auth::expireStorage()" );

		// expire hauth tokens
		Hybrid_Auth::storage()->delete( "hauth_session.hauth_token"        );
		Hybrid_Auth::storage()->delete( "hauth_session.hauth_time"         );

		// expire session, params and data
		Hybrid_Auth::storage()->delete( "hauth_session.is_logged_in"       );
		Hybrid_Auth::storage()->delete( "hauth_session.user.data"          );

		Hybrid_Auth::storage()->delete( "hauth_session.error.status"       );
		Hybrid_Auth::storage()->delete( "hauth_session.error.message"      );
		Hybrid_Auth::storage()->delete( "hauth_session.error.code"         );
		Hybrid_Auth::storage()->delete( "hauth_session.error.trace"        );

		Hybrid_Auth::storage()->delete( "hauth_session.warning"            );

		Hybrid_Auth::storage()->delete( "hauth_session.id_provider_id"     );
		Hybrid_Auth::storage()->delete( "hauth_session.id_provider_params" );

		Hybrid_Auth::storage()->delete( "hauth_session.hauth_endpoint"     );
		Hybrid_Auth::storage()->delete( "hauth_session.hauth_return_to"    );

		// expire tokens
		Hybrid_Auth::storage()->delete( "hauth_session.twitter.oauth_access_token" );
		Hybrid_Auth::storage()->delete( "hauth_session.twitter.oauth_access_token_secret" );
		Hybrid_Auth::storage()->delete( "hauth_session.twitter.oauth_state" );
		Hybrid_Auth::storage()->delete( "hauth_session.twitter.oauth_request_token" );
		Hybrid_Auth::storage()->delete( "hauth_session.twitter.oauth_request_token_secret" );

		Hybrid_Auth::storage()->delete( "hauth_session.friendster.session_key" );
		Hybrid_Auth::storage()->delete( "hauth_session.friendster.auth_token" );
		Hybrid_Auth::storage()->delete( "hauth_session.friendster.session_key" );

		Hybrid_Auth::storage()->delete( "hauth_session.gowalla.access_token" );
 
		Hybrid_Auth::storage()->delete( "hauth_session.linkedin.oauth_token" );
		Hybrid_Auth::storage()->delete( "hauth_session.linkedin.oauth_token_secret" );
		Hybrid_Auth::storage()->delete( "hauth_session.linkedin.oauth_callback_confirmed" );
		Hybrid_Auth::storage()->delete( "hauth_session.linkedin.xoauth_request_auth_url" );
		Hybrid_Auth::storage()->delete( "hauth_session.linkedin.oauth_expires_in" );

		Hybrid_Auth::storage()->delete( "hauth_session.myspaceid.access_token" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.myspaceid.access_secret" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.myspaceid.request_token" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.myspaceid.request_secret" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.myspaceid.callback_confirmed" ); 

		Hybrid_Auth::storage()->delete( "hauth_session.Tumblr.oauth_access_token" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.Tumblr.oauth_access_token_secret" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.Tumblr.oauth_state" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.Tumblr.oauth_request_token" ); 
		Hybrid_Auth::storage()->delete( "hauth_session.Tumblr.oauth_request_token_secret" ); 

		Hybrid_Auth::storage()->delete( "hauth_session.vimeo.oauth_request_token" );  
		Hybrid_Auth::storage()->delete( "hauth_session.vimeo.oauth_request_token_secret" );  
		Hybrid_Auth::storage()->delete( "hauth_session.vimeo.oauth_access_token" );  
		Hybrid_Auth::storage()->delete( "hauth_session.vimeo.oauth_access_token_secret" );  
		
		Hybrid_Auth::storage()->delete( "hauth_session.live.access_token" ); 
	}

   /**
	* Factory for Hybrid_IdProvider classes.
	*
	* First argument may be a string containing the id of the account provider 
	* ("called ID Provider"), e.g. 'Google' corresponds to class Providers_Google.
	* This is case-insensitive and must be allowed in $GLOBAL_HYBRID_AUTH_IDPROVIDERS
	*
	* Second argument may be an associative array of key-value pairs. This is used
	* as the argument to the ID Provider constructor. 
	*
	* @param  mixed $providerId 	String name of ID Provider.
	* @param  mixed $params  		OPTIONAL; an array with ID Provider parameters.
	*
	* @return Provider_Adapter 
	* @return NULL                  If Hybrid_Auth fail to build Hybrid_Provider_Proxy instance
	*/ 
	public static function setup( $providerId, $params = NULL )
	{
		Hybrid_Logger::info( "Enter Hybrid_Auth::setup()", array( $providerId, $params ) );

		// setup will logout any user connect, and rest the session
		Hybrid_Auth::endSession();

		# instantiate a new IDProvider Adapter
		$provider   = new Hybrid_Provider_Adapter();

		# try to setup the Provider obj with given parmas
		try
		{
			if( ! isset( $params["hauth_return_to"] ) ){
				$params["hauth_return_to"] = Hybrid_Auth::getCurrentUrl();
			}
 
			$provider->factory( $providerId, $params );
		}
		catch( Exception $e )
		{
			# if error 
			Hybrid_Auth::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString() ); 

			# and return nil
			return NULL;
		}

		return $provider;
	}

   /**
	* Factory for Hybrid_IdProvider classes.
	*
	* First argument may be a string containing the id of the account provider 
	* ("called ID Provider"), e.g. 'Google' corresponds to class Providers_Google.
	* This is case-insensitive and must be allowed in $GLOBAL_HYBRID_AUTH_IDPROVIDERS
	*
	* Second argument may be an associative array of key-value pairs. This is used
	* as the argument to the ID Provider constructor. 
	*
	* @param  mixed $providerId 	String name of ID Provider. 
	*
	* @return Provider_Adapter 
	* @return NULL                  If Hybrid_Auth fail to build Hybrid_Provider_Proxy instance
	*/ 
	public static function resetup( $providerId )
	{
		$params = Hybrid_Auth::storage()->get( "hauth_session.id_provider_params" );

		Hybrid_Logger::info( "Enter Hybrid_Auth::resetup()", array( $providerId, $params ) );

		# instantiate a new IDProvider Adapter
		$provider   = new Hybrid_Provider_Adapter();

		# try to setup the Provider obj with given parmas
		try
		{ 
			$provider->factory( $providerId, $params );
		}
		catch( Exception $e )
		{
			# if error 
			Hybrid_Auth::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString() ); 

			# and return nil
			return NULL;
		}

		return $provider;
	}

   /**
	* Factory for Hybrid_IdProvider classes.
	*
	* End the user session
	*/ 
	public static function endSession()
	{
		Hybrid_Logger::info( "Enter Hybrid_Auth::endSession()" );

		Hybrid_Auth::expireStorage(); 

		return TRUE;
	}

	// --------------------------------------------------------------------

   /**
	* Wakeup the current user session if true == Hybrid::hasSession() 
	*
	* @param	string
	* @return	Hybrid_Provider_Adapter
	*/
	public static function wakeup( $hauthSession = NULL )
	{

		Hybrid_Logger::info( "Enter Hybrid_Auth::wakeup()", $hauthSession );

		# if user has a session and loggedin IDP service, 
		if( ! Hybrid_Auth::hasSession() )
		{
			return NULL;
		}

		# we restore theProvider internal id (id_provider_id) and params (id_provider_params) 
		$params     = Hybrid_Auth::storage()->get( "hauth_session.id_provider_params" );
		$providerId = Hybrid_Auth::storage()->get( "hauth_session.id_provider_id"     );

		# try to re setup the IDProvider Adapter instance
		$provider = new Hybrid_Provider_Adapter();

		return $provider->factory( $providerId, $params );
	}

	// --------------------------------------------------------------------

   /**
	* Checks to see if there is a session in this PHP page request.
	*
	* @return boolean True if a session is present and current user 
	*         is logged to the IDProvider, false otherwise.
	*/
	public static function hasSession()
	{

		Hybrid_Logger::info( "Enter Hybrid_Auth::hasSession()" );

		# if hauth_session.hauth_token is set on storage system &
		# if hauth_session.hauth_token is equal to current session_id() &
		# if user is loggedin hauth_session.is_logged_in = TRUE
		return 
			( Hybrid_Auth::storage()->get( "hauth_session.hauth_token" ) == session_id() )
			&& 
			Hybrid_Auth::storage()->get( "hauth_session.is_logged_in" ) ;
	}

	// --------------------------------------------------------------------

   /**
	* Checks to see if there is a stored warning.
	*
	* warnings are stored in Hybrid::storage() Hybrid storage system
	* and not displayed directly to user 
	*
	* @return boolean True if there is a warning.
	*/
	public static function hasWarning()
	{

		Hybrid_Logger::info( "Enter Hybrid_Auth::hasWarning()" );

		return 
			(bool) Hybrid_Auth::storage()->get( "hauth_session.warning" );
	}

	// --------------------------------------------------------------------

   /**
	* Add a warning message to warns stak.  
	*/
	public static function addWarning( $message )
	{

		Hybrid_Logger::info( "Enter Hybrid_Auth::addWarning()", $message );

		$_warn = Hybrid_Auth::storage()->get( "hauth_session.warning" );

		$_warn[$message] = time();

		Hybrid_Auth::storage()->set( "hauth_session.warning", $_warn );
	}

	// --------------------------------------------------------------------

   /**
	* a naive warning message getter
	*
	* @return string very short warning message.
	*/
	public static function getWarningMessage()
	{

		Hybrid_Logger::info( "Enter Hybrid_Auth::getWarningMessage()" );

		$_warn = Hybrid_Auth::storage()->get( "hauth_session.warning" );
		$_mesg = "";

		if( is_array( $_warn ) )
		{
			foreach( $_warn as $m => $t )
			{
				$_mesg .= "@$t: $m\n";
			}
		}

		return $_mesg;
	}

	// --------------------------------------------------------------------

   /**
	* store error in HybridAuth cache system
	*/
	public static function setError( $message, $code = NULL, $trace = NULL )
	{
		Hybrid_Logger::info( "Enter Hybrid_Auth::setError()", array( $message, $code, $trace ) );

		Hybrid_Auth::storage()->set( "hauth_session.error.status" , 1        );
		Hybrid_Auth::storage()->set( "hauth_session.error.message", $message );
		Hybrid_Auth::storage()->set( "hauth_session.error.code"   , $code    );
		Hybrid_Auth::storage()->set( "hauth_session.error.trace"  , $trace   );
	}

	// --------------------------------------------------------------------

   /**
	* Checks to see if there is a an error.
	*
	* errors are stored in Hybrid::storage() Hybrid storage system
	* and not displayed directly to user 
	*
	* @return boolean True if there is an error.
	*/
	public static function hasError()
	{

		Hybrid_Logger::info( "Enter Hybrid_Auth::hasError()" );

		return 
			(bool) Hybrid_Auth::storage()->get( "hauth_session.error.status" );
	}

	// --------------------------------------------------------------------

   /**
	* a naive error message getter
	*
	* @return string very short error message.
	*/
	public static function getErrorMessage()
	{

		Hybrid_Logger::info( "Enter Hybrid_Auth::getErrorMessage()" );

		return
			Hybrid_Auth::storage()->get( "hauth_session.error.message" );
	}

	// --------------------------------------------------------------------

   /**
	* a naive error code getter
	*
	* @return int error code defined on Hybrid_Auth.
	*/
	public static function getErrorCode()
	{

		Hybrid_Logger::info( "Enter Hybrid_Auth::getErrorCode()" );

		return 
			Hybrid_Auth::storage()->get( "hauth_session.error.code" );
	}

	// --------------------------------------------------------------------

   /**
	* a naive error backtrace getter
	*
	* @return string detailled error backtrace as string.
	*/
	public static function getErrorTrace()
	{

		Hybrid_Logger::info( "Enter Hybrid_Auth::getErrorTrace()" );

		return 
			Hybrid_Auth::storage()->get( "hauth_session.error.trace" );
	}

	// --------------------------------------------------------------------

   /**
	* Checks to see if there is a an error.
	*
	* errors are stored in Hybrid::storage() Hybrid storage system
	* and not displayed directly to user 
	*
	* @return boolean True if there is an error.
	*/
	public static function redirect( $url )
	{
		GLOBAL $GLOBAL_HYBRID_AUTH_REDIRECT_MODE;

		Hybrid_Logger::info( "Enter Hybrid_Auth::redirect()", $url );

		if( $GLOBAL_HYBRID_AUTH_REDIRECT_MODE == "PHP" )
		{
			header( "Location: $url" ) ;
		}
		elseif( $GLOBAL_HYBRID_AUTH_REDIRECT_MODE == "JS" )
		{
			echo '<html>';
			echo '<head>';
			echo '<script type="text/javascript">';
			echo 'function redirect(){ window.top.location.href="' . $url . '"; }';
			echo '</script>';
			echo '</head>';
			echo '<body onload="redirect()">';
			echo '</body>';
			echo '</html>'; 
		}

		exit( 0 );
	}

	// --------------------------------------------------------------------

   /**
	* @return string the current url for requested PHP page.
	*/
	public static function getCurrentUrl() 
	{

		Hybrid_Logger::info( "Enter Hybrid_Auth::getCurrentUrl()" );

		$scheme = 'http';

		if ( isset( $_SERVER['HTTPS'] ) and $_SERVER['HTTPS'] == 'on' )
		{
			$scheme .= 's';
		}

		return sprintf
		(
			"%s://%s:%s%s"				, 
			$scheme						, 
			$_SERVER['SERVER_NAME']		, 
			$_SERVER['SERVER_PORT']		, 
			$_SERVER['PHP_SELF']
		); 
	}

	// --------------------------------------------------------------------

   /**
	* @return string associated text message in HybridAuth lang file
	*/
	public static function text( $id ) 
	{
		GLOBAL $GLOBAL_HYBRID_AUTH_TEXT, $GLOBAL_HYBRID_AUTH_TEXT_LANG;

		Hybrid_Logger::info( "Enter Hybrid_Auth::text()", $id );

		if ( isset( $GLOBAL_HYBRID_AUTH_TEXT[$GLOBAL_HYBRID_AUTH_TEXT_LANG][$id] ) )
		{
			return $GLOBAL_HYBRID_AUTH_TEXT[$GLOBAL_HYBRID_AUTH_TEXT_LANG][$id];
		}

		return  NULL; 
	}
}
