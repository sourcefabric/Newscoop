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
 * PHP Session storage 
 *
 * @author Zachy <hybridauth@gmail.com>
 */
class Hybrid_Storage_Session extends Hybrid_Storage
{
	var $storageKey = NULL;
 
	public function get($key, $expiration = false) 
	{
		$key = $this->storageKey . ":" . $key;

		if( isset( $_SESSION[$key] ) ) 
		{ 
			return $_SESSION[$key] ; 
		}

		return NULL; 
	}

	public function set($key, $value)
	{
		$key = $this->storageKey . ":" . $key;

		$_SESSION[$key] = $value;
	}

	function delete($key)
	{
		if( isset( $_SESSION[$key] ) ) 
		{ 
			unset( $_SESSION[$key] );
		} 
	} 
}
