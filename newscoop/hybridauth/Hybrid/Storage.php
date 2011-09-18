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

/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/* Basic exception classes */
class storageException extends Exception {}

/**
 * Abstract storage class
 *
 * @author Chris Chabot
 */
abstract class Hybrid_Storage {
	var $storageKey = NULL;

	/**
	* Retrieves the data for the given key, or false if they
	* key is unknown or expired
	*
	* @param String $key The key who's data to retrieve
	* @param int $expiration Experiration time in seconds
	*/
	abstract protected function get($key, $expiration = false);

	/**
	* Store the key => $value set. The $value is serialized
	* by this function so can be of any type
	*
	* @param String $key Key of the data
	* @param Any-type $value the data
	*/
	abstract protected function set($key, $value);

	/**
	* Removes the key/data pair for the given $key
	*
	* @param String $key
	*/
	abstract protected function delete($key);
}


