<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * A framework for authentication and authorization in PHP applications
 *
 * LiveUser is an authentication/permission framework designed
 * to be flexible and easily extendable.
 *
 * Since it is impossible to have a
 * "one size fits all" it takes a container
 * approach which should enable it to
 * be versatile enough to meet most needs.
 *
 * PHP version 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston,
 * MA  02111-1307  USA
 *
 *
 * @category authentication
 * @package LiveUser
 * @author  Markus Wolff <wolff@21st.de>
 * @author  Helgi Þormar Þorbjörnsson <dufuz@php.net>
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @author  Arnaud Limbourg <arnaud@php.net>
 * @author  Pierre-Alain Joye <pajoye@php.net>
 * @author  Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version CVS: $Id: Common.php,v 1.59 2006/08/15 06:43:20 mahono Exp $
 * @link http://pear.php.net/LiveUser
 */

/**
 * This class provides a set of functions for implementing a user
 * authorisation system on live websites. All authorisation
 * backends/containers must be inherited from this base class.
 *
 * @category authentication
 * @package LiveUser
 * @author   Markus Wolff <wolff@21st.de>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser_Auth_Common
{
    /**
     * Has the current user successfully logged in?
     *
     * @var    bool
     * @access public
     * @see    LiveUser_Auth_Common::isActive
     */
    var $loggedIn = null;

    /**
     * Timestamp of current login (last to be written)
     *
     * @var    int
     * @access public
     */
    var $currentLogin = 0;

    /**
     * Auth maximum lifetime in seconds
     *
     * If this variable is set to 0, auth never expires
     *
     * @var    int
     * @access public
     */
    var $expireTime = 0;

    /**
     * Maximum time of idleness in seconds
     *
     * Idletime gets refreshed each time, init() is called. If this
     * variable is set to 0, idle time is never checked.
     *
     * @var    int
     * @access public
     */
    var $idleTime = 0;

    /**
     * Possible encryption modes.
     *
     * @var    array
     * @access public
     */
    var $encryptionModes = array(
        'MD5'   => 'MD5',
        'PLAIN' => 'PLAIN',
        'RC4'   => 'RC4',
        'SHA1'  => 'SHA1'
    );

    /**
     * Defines the algorithm used for encrypting/decrypting passwords.
     *
     * @var    string
     */
    var $passwordEncryptionMode = 'MD5';

    /**
     * Defines the secret to use for encryption if needed
     *
     * @var    string
     * @access public
     */
    var $secret = '';

    /**
     * Error stack
     *
     * @var    PEAR_ErrorStack
     * @access public
     */
    var $stack = null;

    /**
    * Array of all the user data read from the backend database
    *
    * @var array
    * @access public
    */
    var $propertyValues = array();

    /**
     * The name associated with this auth container. The name is used
     * when adding users from this container to the reference table
     * in the permission container. This way it is possible to see
     * from which auth container the user data is coming from.
     *
     * @var    string
     * @access public
     */
    var $containerName = null;

    /**
     * External values to check (config settings)
     *
     * @var    array
     * @access public
     */
    var $externalValues = array();

    /**
     * A list of handle fields that are used to find a user.
     *
     * @var    array
     * @access public
     */
    var $handles = array('handle');

    /**
     * Table configuration
     *
     * @var    array
     * @access public
     */
    var $tables = array();

    /**
     * All fields with their types
     *
     * @var    array
     * @access public
     */
    var $fields = array();

    /**
     * All fields with their alias
     *
     * @var    array
     * @access public
     */
    var $alias = array();

    /**
     * Class constructor. Feel free to override in backend subclasses.
     *
     * @var    array configuration options
     * @return void
     *
     * @access protected
     */
    function LiveUser_Auth_Common()
    {
        $this->stack = &PEAR_ErrorStack::singleton('LiveUser');
    }

    /**
     * Load the storage container
     *
     * @param   array  array containing the configuration.
     * @param   string name of the container that should be used
     * @return  bool true on success or false on failure
     *
     * @access public
     */
    function init($conf, $containerName)
    {
        $this->containerName = $containerName;
        if (is_array($conf)) {
            $keys = array_keys($conf);
            foreach ($keys as $key) {
                if (isset($this->$key)) {
                    $this->$key =& $conf[$key];
                }
            }
        }

        if (array_key_exists('storage', $conf) && is_array($conf['storage'])) {
            $keys = array_keys($conf['storage']);
            foreach ($keys as $key) {
                if (isset($this->$key)) {
                    $this->$key =& $conf['storage'][$key];
                }
            }
        }

        require_once 'LiveUser/Auth/Storage/Globals.php';
        if (empty($this->tables)) {
            $this->tables = $GLOBALS['_LiveUser']['auth']['tables'];
        } else {
            $this->tables = LiveUser::arrayMergeClobber($GLOBALS['_LiveUser']['auth']['tables'], $this->tables);
        }
        if (empty($this->fields)) {
            $this->fields = $GLOBALS['_LiveUser']['auth']['fields'];
        } else {
            $this->fields = LiveUser::arrayMergeClobber($GLOBALS['_LiveUser']['auth']['fields'], $this->fields);
        }
        if (empty($this->alias)) {
            $this->alias = $GLOBALS['_LiveUser']['auth']['alias'];
        } else {
            $this->alias = LiveUser::arrayMergeClobber($GLOBALS['_LiveUser']['auth']['alias'], $this->alias);
        }
    }

    /**
     * store all properties in an array
     *
     * @return  array
     *
     * @access public
     */
    function freeze()
    {
        // get values from $this->externalValues['values'] and
        // store them into $this->propertyValues['storedExternalValues']
        $this->setExternalValues();

        $propertyValues = array(
            'propertyValues'    => $this->propertyValues,
            'loggedIn'          => $this->loggedIn,
            'currentLogin'      => $this->currentLogin,
        );

        return $propertyValues;
    }

    /**
     * Reinitializes properties
     *
     * @param   array  $propertyValues
     * @return  bool
     *
     * @access public
     */
    function unfreeze($propertyValues)
    {
         foreach ($propertyValues as $key => $value) {
             $this->{$key} = $value;
         }

        return $this->externalValuesMatch();
    }

    /**
     * Decrypts a password so that it can be compared with the user input.
     * Uses the algorithm defined in the passwordEncryptionMode property.
     *
     * @param  string the encrypted password
     * @return string the decrypted password
     *
     * @access public
     */
    function decryptPW($encryptedPW)
    {
        return LiveUser::decryptPW($encryptedPW, $this->passwordEncryptionMode, $this->secret);
    }

    /**
     * Encrypts a password for storage in a backend container.
     * Uses the algorithm defined in the passwordEncryptionMode property.
     *
     * @param string  encryption type
     * @return string the encrypted password
     *
     * @access public
     */
    function encryptPW($plainPW)
    {
        return LiveUser::encryptPW($plainPW, $this->passwordEncryptionMode, $this->secret);
    }

    /**
     * Tries to make a login with the given handle and password.
     * A user can't login if he's not active.
     *
     * @param  string   user handle
     * @param  string   user password
     * @param  bool|int if the user data should be read using the auth user id
     * @return bool null when user is inactive, true on success or false on failure
     *
     * @access public
     */
    function login($handle, $passwd, $auth_user_id = false)
    {
        // Init value: Is user logged in?
        $this->loggedIn = false;

        // Read user data from database
        $result = $this->readUserData($handle, $passwd, $auth_user_id);
        if (!$result) {
            return $result;
        }

        // If login is successful (user data has been read)
        // ...we still need to check if this user is declared active
        if (!array_key_exists('is_active', $this->propertyValues)
            || $this->propertyValues['is_active']
        ) {
            // ...and if so, we have a successful login (hooray)!
            $this->loggedIn = true;
            $this->currentLogin = time();
        }

        // In case Login was successful update user data
        if ($this->loggedIn) {
            $this->_updateUserData();
        }

        return true;
    }

    /**
     * Writes current values for user back to the database.
     * This method does nothing in the base class and is supposed to
     * be overridden in subclasses according to the supported backend.
     *
     * @return bool true on success or false on failure
     *
     * @access private
     */
    function _updateUserData()
    {
        $this->stack->push(LIVEUSER_ERROR_NOT_SUPPORTED, 'exception',
            array('feature' => '_updateUserData')
        );
        return false;
    }

    /**
     * Reads user data from the given data source
     * If only $handle is given, it will read the data
     * from the first user with that handle and return
     * true on success.
     * If $handle and $passwd are given, it will try to
     * find the first user with both handle and password
     * matching and return true on success (this allows
     * multiple users having the same handle but different
     * passwords - yep, some people want this).
     * if only an auth_user_id is passed it will try to read the data based on the id
     * If no match is found, false is being returned.
     *
     * Again, this does nothing in the base class. The
     * described functionality must be implemented in a
     * subclass overriding this method.
     *
     * @param  string user handle
     * @param  string user password
     * @param  bool|int if the user data should be read using the auth user id
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function readUserData($handle = '', $passwd = '', $auth_user_id = false)
    {
        $this->stack->push(LIVEUSER_ERROR_NOT_SUPPORTED, 'exception',
            array('feature' => 'readUserData')
        );
        return false;
    }

    /**
     * Function returns the inquired value if it exists in the class.
     *
     * @param  string  name of the property to be returned.
     * @return mixed   null, a scalar or an array.
     *
     * @access public
     */
    function getProperty($what)
    {
        $that = null;
        if (array_key_exists($what, $this->propertyValues)) {
            $that = $this->propertyValues[$what];
        } elseif (isset($this->$what)) {
            $that = $this->$what;
        }
        return $that;
    }

    /**
     * Creates associative array of values from $externalValues['values'] with $keysToCheck
     *
     * @return void
     *
     * @access public
     */
    function setExternalValues()
    {
        if (array_key_exists('keysToCheck', $this->externalValues)
            && is_array($this->externalValues['keysToCheck'])
        ) {
            foreach ($this->externalValues['keysToCheck'] as $keyToCheck) {
                if (array_key_exists($keyToCheck, $this->externalValues['values'])) {
                    $this->propertyValues['storedExternalValues'][$keyToCheck] =
                        md5($this->externalValues['values'][$keyToCheck]);
                }
            }
        }
    }

    /**
     * Check if the stored external values match the current external values
     *
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function externalValuesMatch()
    {
        if (array_key_exists('storedExternalValues', $this->propertyValues)
            && is_array($this->propertyValues['storedExternalValues'])
        ) {
            foreach ($this->propertyValues['storedExternalValues'] as $keyToCheck => $storedValue) {
                // return false if any one of the stored values does not match the current value
                if (!array_key_exists($keyToCheck, $this->externalValues['values'])
                    || md5($this->externalValues['values'][$keyToCheck]) != $storedValue
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * properly disconnect from resources
     *
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function disconnect()
    {
        return true;
    }

}
?>
