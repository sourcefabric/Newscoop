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
 * The Hybrid_User class represents the current loggedin user
 *
 * Note: As with all APIs, we are limited by the amout of data which the API provider provides us.
 *
 * @package    Hybrid_Auth 
 * @author     Zachy <hybridauth@gmail.com>
 * @version    1.0
 * @since      HybridAuth 1.0.1 
 * @link       http://hybridauth.sourceforge.net/userguide/Profile_Data.html
 * @link       http://hybridauth.sourceforge.net/userguide/Profile_Data_User_Profile.html
 * @see        Hybrid_User_Profile
 * @see        Hybrid_User_Contacts
 */
class Hybrid_User 
{
   /**
	* The ID (name) of the connected provider
	*/
	var $providerId   = NULL;
 
   /**
	* Unique session ID for the connected user, eq to session_id().
	*/
	var $UID          = NULL;
 
   /**
	* The user's ID on the connected provider
	*/
	var $providerUID  = NULL;
 
   /**
	* The ID (name) of the connected provider
	*/
	var $timestamp    = NULL; 
 
   /**
	* True if $GLOBAL_HYBRID_AUTH_BYPASS_EP_MODE = TRUE and the current profile is populated by some auto generated data
	*/
	var $populated    = NULL;

   /**
	* user profile, containts the list of fields available in the normalized user profile structure used by HybridAuth.
	*/
	var $profile      = NULL;

   /**
	* user contacts list, for future use
	*/
	# for future use, HybridAuth dont provide users contats on this version
	#     var $contacts     = NULL;

   /**
	* inisialize the user object,
	*/
	function __construct( $providerId )
	{
		$this->timestamp    = time();
		$this->providerId   = $providerId;
		$this->UID          = session_id();

		$this->profile      = new Hybrid_User_Profile();

		# for future use, HybridAuth dont provide users contats on this version
		#      $this->contacts     = new Hybrid_User_Contacts();
	}

	// --------------------------------------------------------------------

   /**
	* naive getter for the user profile ($this->profile member)
	*  
	* @see Hybrid_User_Profile 
	* @return object Hybrid_User_Profile 
	*/
	public function getProfile()
	{
		return $this->profile;
	}

	// --------------------------------------------------------------------

   /**
	* naive getter for the user contacts list ($this->contacts member)
	* 
	* Note: HybridAuth dont provide users contats on version 1.0.x
	*  
	* @see Hybrid_User_Contacts 
	* @return object Hybrid_User_Contacts 
	*/
	# for future use, HybridAuth dont provide users contats on this version
	#    public function getContacts()
	#    {
	#    	return $this->contacts;
	#    }

	// --------------------------------------------------------------------

   /**
	* Populate the user profile with some auto generated data
	*/
	function populateProfile()
	{
		$rand_user_id = strtoupper( md5( rand() . time() ) );

		$this->populated            = TRUE;
		$this->providerUID          = "HYBRIDAUTH_TEST_" . $rand_user_id;
		$this->profile->lastName    = "Test";
		$this->profile->firstName   = "User " . $rand_user_id;
		$this->profile->displayName = "Test User";
		$this->profile->description = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor ...";
		$this->profile->age         = rand(13, 79);
		$this->profile->birthDay    = rand(1, 31) ;
		$this->profile->birthMonth  = rand(1, 12) ;
		$this->profile->birthYear   = date("Y") - $this->profile->age ;
		$this->profile->language    = rand(0, 1) ? "en" : "es" ;
		$this->profile->gender      = rand(0, 1) ? "male" : "female" ;
		$this->profile->country     = rand(0, 1) ? "US" : "JP" ;
		$this->profile->zip         = rand() ;
		$this->profile->address     = $this->profile->country . " " . $this->profile->zip; 

		$this->profile->email       = "user.". $rand_user_id . "@" . strtolower( $this->providerId ) . ".com" ;
		$this->profile->webSiteURL  = "http://www." . strtolower( $this->providerId ) . ".com/";
		$this->profile->profileURL  = "http://www." . strtolower( $this->providerId ) . ".com/user/profile/" . $rand_user_id ;
		$this->profile->photoURL    = "http://www." . strtolower( $this->providerId ) . ".com/user/img/" . $rand_user_id ;
	}
}
