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
 * @package		Hybrid_Auth
 * @author		hybridAuth Dev Team
 * @copyright	Copyright (c) 2009, hybridAuth Dev Team.
 * @license		http://hybridauth.sourceforge.net/licenses.html under MIT and GPL
 * @link		http://hybridauth.sourceforge.net 
 */
// ------------------------------------------------------------------------

   /**
	* hybridAuth endpoint page
	* 
	*  # define:endpoint 
	*  		When a user logs into IDp and grants your application permission to access their data, 
	*  		the IDp redirects the user to your application's endpoint URL with some extra GET/POST
	*  		parameters attached 
	*  
	*  # define:hybrid.endpoint.php
	*  		is the endpoint URL to your application. is used as a proxy bettwen the IDp and your web site
	* 	
	*  when a user select and IDp form (let say YOUR_LOGIN_PAGE.PHP):
	*  1.  the object intanciedted by Hybrid_Auth::setup( idprovider, params ) in YOUR_LOGIN_PAGE.PHP
	*      will build an url then redirect the user to HYBRID.ENDPOINT.PHP (or $HYBRID_AUTH_HYBRID_URL_EP)
	* 
	*  2.  HYBRID.ENDPOINT.PHP will check somes parameters and resetup Hybrid_Auth from the stored session,
	*  
	*  3.  and redirect again the user to the IDp identification page, where user can grants or not access to their 
	*      data 
	*  
	*  3.1 if user don't grants access to your site, some IDp will callback HYBRID.ENDPOINT.PHP in what they call
	*      "cancel mode". HYBRID.ENDPOINT.PHP will recallback YOUR_LOGIN_PAGE.PHP where Hybrid_Auth::hasSession()
	*      will return FALSE, and error/reason stored in Hybrid_Auth::getErrorCode(), Hybrid_Auth::getErrorMessage() 
	* 
	*  3.2 if user grants access to your site, IDp will callback HYBRID.ENDPOINT.PHP with some extra GET/POST parameters 
	*      attached, HYBRID.ENDPOINT.PHP will recallback YOUR_LOGIN_PAGE.PHP where Hybrid_Auth::hasSession() will return 
	*      TRUE, and profil/contacts stored in Hybrid_IdProvider::getUserProfile(), Hybrid_IdProvider::getUserContacts()
	* 
	*  this page can be called only by using Hybrid_IdProvider::login(), and can't be directly
	*  accessed by users 
	* 
	* @author     Zachy <hybridauth@gmail.com>
	* @version    
	* @since      Version 1.0
	*/

// ------------------------------------------------------------------------

	Hybrid_Logger::info( "Enter Endpoint.php" ); 

	# define:hybrid.endpoint.php step 3.
	# yeah, why not a switch!
	if( isset( $_REQUEST["hauth_start"] ) && $_REQUEST["hauth_start"] )
	{
		# define:hybrid.endpoint.php step 2.
		$hauth = Hybrid_Auth::resetup( $_REQUEST["hauth_start"] );

		# if REQUESTed hauth_idprovider is wrong, session not created, or shit happen, etc.
		# note: your are free to change the error message in ./library/Resources/lang.ini
		if( ! $hauth )
		{
			Hybrid_Logger::error( Hybrid_Auth::text( "HYBRID_ENDPOINT_WRONG_PARAMS" ) );

			die( Hybrid_Auth::text( "HYBRID_ENDPOINT_WRONG_PARAMS" ) );
		}

		try
		{ 
			Hybrid_Logger::debug( "Call [{$hauth->adapter->providerId}]::loginBegin(), received http request", $_REQUEST );

			Hybrid_Logger::info( "Endpoint.php, reSet user to deconnected" );
			Hybrid_Auth::storage()->set( "hauth_session.is_logged_in", 0 );
			Hybrid_Auth::storage()->set( "hauth_session.user.data", NULL );

			$hauth->adapter->loginBegin();
		}
		catch( Exception $e )
		{
			Hybrid_Logger::error( "Exception:" . $e->getMessage(), $e );

			Hybrid_Auth::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString() );

			$hauth->returnToCallbackUrl();
		}

		exit( 0 );
	}

	# define:hybrid.endpoint.php step 3.1 and 3.2
	if( isset( $_REQUEST["hauth_done"] ) && $_REQUEST["hauth_done"] ) 
	{
		$hauth = Hybrid_Auth::resetup( $_REQUEST["hauth_done"] );

		if( ! $hauth )
		{
			Hybrid_Logger::error( Hybrid_Auth::text( "HYBRID_ENDPOINT_WRONG_PARAMS" ) );
			
			die( Hybrid_Auth::text( "HYBRID_ENDPOINT_WRONG_PARAMS" ) );
		}

		try
		{
			Hybrid_Logger::debug( "Call [{$hauth->adapter->providerId}]::loginFinish(), received http request", $_REQUEST );

			$hauth->adapter->loginFinish(); 
		}
		catch( Exception $e )
		{
			Hybrid_Logger::error( "Exception:" . $e->getMessage(), $e );

			Hybrid_Auth::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString() );
		}

		$hauth->returnToCallbackUrl();

		exit( 0 );
	}

	# probably never reached, but who know
	# note: once again, your are free to change this error message
	Hybrid_Logger::error( Hybrid_Auth::text( "HYBRID_ENDPOINT_FORBIDDEN" ) );

	die( Hybrid_Auth::text( "HYBRID_ENDPOINT_FORBIDDEN" ) );
