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
 
/**
 * Logging wrapper for the Yahoo objects.
 *
 * Logging wrapper for the Yahoo objects.
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.0.1 
 */
class Hybrid_Logger
{
    /**
     * Log a message at the debug level.
     *
     * @param $message The message to log.
     */
    public static function debug($message, $object = NULL)
	{
        GLOBAL $GLOBAL_HYBRID_AUTH_DEBUG_MODE;
        GLOBAL $GLOBAL_HYBRID_AUTH_DEBUG_FILE;

		if( $GLOBAL_HYBRID_AUTH_DEBUG_MODE )
		{
		    $datetime = new DateTime();
		    $datetime =  $datetime->format(DATE_ATOM);
    
			file_put_contents
			( 
				$GLOBAL_HYBRID_AUTH_DEBUG_FILE, 
				"DEBUG -- " . $_SERVER['REMOTE_ADDR'] . " -- " . $datetime . " -- " . $message . " -- " . print_r($object, true) . "\n", 
				FILE_APPEND
			);
        }
    }

	// --------------------------------------------------------------------

    /**
     * Log a message at the info level.
     *
     * @param $message The message to log.
     */
    public static function info($message, $object = NULL)
	{
        GLOBAL $GLOBAL_HYBRID_AUTH_DEBUG_MODE;
        GLOBAL $GLOBAL_HYBRID_AUTH_DEBUG_FILE;

		if( $GLOBAL_HYBRID_AUTH_DEBUG_MODE )
		{
		    $datetime = new DateTime();
		    $datetime =  $datetime->format(DATE_ATOM);
    
			file_put_contents
			( 
				$GLOBAL_HYBRID_AUTH_DEBUG_FILE, 
				"INFO -- " . $_SERVER['REMOTE_ADDR'] . " -- " . $datetime . " -- " . $message . " -- " . print_r($object, true) . "\n", 
				FILE_APPEND
			);
        }
    }

	// --------------------------------------------------------------------

    /**
     * Log a message at the error level.
     *
     * @param $message The message to log.
     */
    public static function error($message, $object = NULL)
	{
        GLOBAL $GLOBAL_HYBRID_AUTH_DEBUG_MODE;
        GLOBAL $GLOBAL_HYBRID_AUTH_DEBUG_FILE;

		if( $GLOBAL_HYBRID_AUTH_DEBUG_MODE )
		{
		    $datetime = new DateTime();
		    $datetime =  $datetime->format(DATE_ATOM);
    
			file_put_contents
			( 
				$GLOBAL_HYBRID_AUTH_DEBUG_FILE, 
				"ERROR -- " . $_SERVER['REMOTE_ADDR'] . " -- " . $datetime . " -- " . $message . " -- " . print_r($object, true) . "\n", 
				FILE_APPEND
			);
        }
    }

	// --------------------------------------------------------------------

    /**
     * Enables/disables session debugging.
     *
     * @param $debug Boolean to enable/disable debugging.
     */
    public static function setDebug( $debugMode )
	{
        global $GLOBAL_HYBRID_AUTH_DEBUG_MODE;

	   $GLOBAL_HYBRID_AUTH_DEBUG_MODE = (bool) $debugMode;
    }
}
