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
 * Hybrid_User_Profile describe the list of fields available in the normalized user profile
 * structure used by HybridAuth.
 *
 * Hybrid_User_Profile can be accessed via Hybrid_User::$profile or by the navie getter Hybrid_User::getProfile()
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.0.1 
 * @link       http://hybridauth.sourceforge.net/userguide/Profile_Data.html
 * @link       http://hybridauth.sourceforge.net/userguide/Profile_Data_User_Profile.html
 * @see        Hybrid_User
 * @see        Hybrid_User_Contacts
 */
class Hybrid_User_Profile
{
   /**
	* User website, blog, web page, 
	*/	
	var $webSiteURL 	= NULL;

   /**
	* URL link to profile page on the IDp web site 
	*/
	var $profileURL 	= NULL;

   /**
	* URL link to user photo or avatar 
	*/	
	var $photoURL 		= NULL;

   /**
	* User dispalyName provided by the IDp or a concatenation of first and last name. 
	*/
	var $displayName 	= NULL;

   /**
	* A short about_me 
	*/
	var $description 	= NULL;

   /**
	* User's first name 
	*/
	var $firstName   	= NULL;

   /**
	* User's last name 
	*/
	var $lastName 		= NULL;

   /**
	* male or female 
	*/
	var $gender 		= NULL;

   /**
	* language
	*/
	var $language 		= NULL;

   /**
	* User age, we dont calculate it. we return it as is if the IDp provide it.
	*/
	var $age 			= NULL;

   /**
	* User birth Day, we dont calculate it. we return it as is if the IDp provide it.
	*/
	var $birthDay 		= NULL;

   /**
	* User birth Month, we dont calculate it. we return it as is if the IDp provide it.
	*/
	var $birthMonth 	= NULL;

   /**
	* User birth Year, we dont calculate it. we return it as is if the IDp provide it.
	*/
	var $birthYear 		= NULL;

   /**
	* User email. Not all of IDp garant access to the user email
	*/
	var $email 			= NULL;

   /**
	*  phone number
	*/
	var $phone 			= NULL;

   /**
	* complete user address
	*/
	var $address 		= NULL;

   /**
	* user country
	*/
	var $country 		= NULL;

   /**
	* region
	*/
	var $region			= NULL;

   /**
	*  city
	*/
	var $city 			= NULL;

   /**
	* Postal code or zipcode. 
	*/
	var $zip 			= NULL;

	// --------------------------------------------------------------------

   /**
	* calculate the user age from his birthday,
	* for future use,
	*/
	public function calculateAge()
	{
	}

	// --------------------------------------------------------------------

   /**
	* explode date of birth to day, month and year 
	* for future use,
	*/
	public function calculateDOB()
	{
	}
}
