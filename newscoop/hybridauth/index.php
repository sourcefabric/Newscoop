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
	 * here we implement some needed stuff for OpenID
	 * - policy
	 * - Relying Party Discovery for OpenID
	 *
	 *	# http://blog.nerdbank.net/2008/06/why-yahoo-says-your-openid-site.html
	 *	   You must advertise your XRDS document from your Realm URL
	 *	   Add the following line inside the HEAD tags of your Realm page:
	 *	   <meta http-equiv="X-XRDS-Location" content="http://yourdomain.com/hybridauth_path/or_somthing_like_that/?openid_xrds"/> 
	 *
	 *  # http://developer.yahoo.com/openid/faq.html
	 *	   Yahoo! displays the above warning for Relying Parties which fail to implement Section 13: Discovering OpenID Relying Parties of the 
	 *	   OpenID 2.0 Protocol. Implementing Relying Party Discovery enables Yahoo to verify your site's OpenID Realm when 
	 *	   servicing Authentication Requests from your site. 
	 * 
	 * @author     Zachy <hybridauth@gmail.com>
	 * @see        Section 13: Discovering OpenID Relying Parties of the OpenID 2.0 Protocol
	 * @link       http://wiki.openid.net
	 * @link       http://developer.yahoo.com/openid/faq.html
	 * @link       http://blog.nerdbank.net/2008/06/why-yahoo-says-your-openid-site.html
	 */

// ------------------------------------------------------------------------

	include "./hybridauth.php";

	Hybrid_Logger::debug( "Enter index.php", $_SERVER ); 

	# if openid_policy get argument defined, we return our policy document  
	if( isset( $_REQUEST["hauth_start"] ) || isset( $_REQUEST["hauth_done"] ) )
	{
		require $GLOBAL_HYBRID_AUTH_PATH_EP . "Hybrid/endpoint.php";

		exit( 0 );
	}

	# if windows_live_channel get argument defined, we return our windows_live WRAP_CHANNEL_URL
	if( isset( $_REQUEST["get"] ) && $_REQUEST["get"] == "windows_live_channel")
	{
		echo file_get_contents( $GLOBAL_HYBRID_AUTH_PATH_RESOURCES . "windows_live_channel.html" ); 

		exit( 0 );
	}

	# if openid_policy get argument defined, we return our policy document  
	if( isset( $_REQUEST["get"] ) && $_REQUEST["get"] == "openid_policy")
	{
		echo file_get_contents( $GLOBAL_HYBRID_AUTH_PATH_RESOURCES . "openid_policy.html" ); 

		exit( 0 );
	}

	# if openid_xrds get argument defined, we return our XRDS document  
	# for sure you can change "openid_xrds" by any name, just sync it with the X-XRDS-Location tag
	if( isset( $_REQUEST["get"] ) && $_REQUEST["get"] == "openid_xrds")
	{
		header("Content-Type: application/xrds+xml");

		echo str_replace
			(
				"{RETURN_TO_URL}",
				$GLOBAL_HYBRID_AUTH_URL_EP,
				file_get_contents( $GLOBAL_HYBRID_AUTH_PATH_RESOURCES . "openid_xrds.xml" )
			); 

		exit( 0 );
	}

	# Else, 
	# We advertise our XRDS document, something supposed to be done from the Realm URL page
	# The Realm URL is $GLOBAL_HYBRID_AUTH_URL_BASE in configuration file hybrid.config.php
	$x_xrds_location = $GLOBAL_HYBRID_AUTH_URL_BASE . "?get=openid_xrds";
	
	echo str_replace
		(
			"{X_XRDS_LOCATION}",
			$x_xrds_location,
			file_get_contents( $GLOBAL_HYBRID_AUTH_PATH_RESOURCES . "openid_realm.html" )
		);
