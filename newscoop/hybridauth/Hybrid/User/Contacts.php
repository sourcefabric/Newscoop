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
 * The Hybrid_User_Contacts class contain the contacts list of the current loggedin user
 * 
 * Hybrid_User_Contacts can be accessed via Hybrid_User::$contacts or by the navie getter Hybrid_User::gelContacts()
 * Note: HybridAuth dont provide users contats on version 1.0.x
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.0.1
 * @link       http://hybridauth.sourceforge.net/userguide/Profile_Data.html
 * @see        Hybrid_User 
 * @see        Hybrid_User_Contacts
 */
class Hybrid_User_Contacts
{
	var $startIndex   = NULL; 
	var $itemsPerPage = NULL; 
	var $totalResults = NULL;

	var $sorted       = FALSE; 
	var $filtered     = FALSE; 

   /**
	*   {
	*       "id": "78609843204328432"
	*		,
	*       "displayName": "toto tati"
	*		,
	*       "emails": 
	*	[
	*		{
	*			"type": "home",
	*			"value": "toto@example.com"
	*		}
	*		,
	*		{
	*			"type": "other",
	*			"value": "tati@example.com"
	*		}
	*       ]
	*   }
	*/	
	var $entries      = ARRAY();

	// --------------------------------------------------------------------

   /**
	* return the next set of contacts by $itemsPerPage,
	* for future use,
	*/
	public function next()
	{
	}

	// --------------------------------------------------------------------

   /**
	* rewind contacts list
	* for future use,
	*/
	public function rewind()
	{
	}

	// --------------------------------------------------------------------

   /**
	* find a contact entry by value, or type
	* for future use,
	*/
	public function seek()
	{
	}
}
