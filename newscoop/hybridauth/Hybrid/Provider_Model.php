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
 * The Providers_Model class is a simple abstract model for providers wrappers 
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.0.1 
 * @link       http://hybridauth.sourceforge.net/userguide/Supported_identity_providers_and_setup_keys.html
 * @see        Hybrid_Provider_Adapter
 */
abstract class Hybrid_Provider_Model
{
   /**
	* Hybrid_User obj, represents the current user
	*/
	var $user             = NULL;

   /**
	* IDp adapter config on hybridauth.php
	*/
	var $config           = NULL;

   /**
	* IDp adapter requireds params
	*/
	var $params           = NULL;

   /**
	* IDp ID (or unique name)
	*/
	var $providerId       = NULL; 

   /**
	* the IDp api client (optional)
	*/
	var $api              = NULL; 

   /**
	* common IDp wrappers constructor
	*/
	function __construct( $providerId, $config, $params = NULL )
	{
		Hybrid_Logger::info( "Enter [{$this->providerId}]::__construct()" );
		
		$this->config     = $config;
		$this->providerId = $providerId;

		# init the IDp adapter parameters, get them from the cache if possible
		if( ! $params )
		{
			$this->params = Hybrid_Auth::storage()->get( "hauth_session.id_provider_params" );
		}
		else
		{
			$this->params = $params;
		}

		Hybrid_Logger::debug( "[{$this->providerId}]::__construct(), set wrapper params", $this->params );

		# init the current user data container, get them from the cache if possible
		if( Hybrid_Auth::storage()->get( "hauth_session.user.data" ) )
		{
			$this->user = Hybrid_Auth::storage()->get( "hauth_session.user.data" );
		}
		else
		{
			$this->user = new Hybrid_User( $this->providerId );
		}

		Hybrid_Logger::debug( "[{$this->providerId}]::__construct(), set user data", $this->user );

		$this->initialize();

		Hybrid_Logger::debug( "[{$this->providerId}]::__construct(), wrapper initialized", $this->api );
	}

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
	abstract protected function initialize(); 

	// --------------------------------------------------------------------

   /**
	* begin login 
	*/
	abstract protected function loginBegin();

	// --------------------------------------------------------------------

   /**
	* finish login
	*/
	abstract protected function loginFinish();

	// --------------------------------------------------------------------

   /**
	* sign out the user from the IDp if possible, and erase his local session 
	*/
	abstract protected function logout();

	// --------------------------------------------------------------------

   /**
	* a common Method, just expire local user session, but dont logout the user from the IDp 
	*/
	function deconnect()
	{ 
		Hybrid_Auth::expireStorage();

		return TRUE;
	}

	// --------------------------------------------------------------------

   /**
	* load the user profile from the IDp api client
	*/
	abstract protected function getUserProfile();

	// --------------------------------------------------------------------

   /**
	* load the current logged in user contacts list from the IDp api client 
	*
	* HybridAuth dont provide users contats on version 1.0.x
	*/
	function getUserContacts() 
	{
		# to access this area, the user have to be loggedin with an IDp an has a HybridAuth session
		if ( ! Hybrid_Auth::hasSession() )
		{
			throw new Exception( "HybridAuth can't access user profile data. The current user have to sign in with [{$this->providerId}] before any request!" );
		}

		Hybrid_Auth::addWarning( Hybrid_Auth::text( "GET_USER_CONTACTS_UNSUPPORTED_WARNING" ) ); 

		return NULL;
	}
}
