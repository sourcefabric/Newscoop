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
 * @version CVS: $Id: PEARAuth.php,v 1.39 2006/04/13 15:26:49 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */

/**
 * Require parent class definition and PEAR::Auth class.
 */
require_once 'LiveUser/Auth/Common.php';
require_once 'Auth.php';

/**
 * PEAR_Auth container for Authentication
 *
 * This is a PEAR::Auth backend driver for the LiveUser class.
 * The general options to setup the PEAR::Auth class can be passed to the constructor.
 * To choose the right auth container and options, you have to set 'container'
 * and 'options' respectively in the storage array.
 *
 * Requirements:
 * - File "LiveUser.php" (contains the parent class "LiveUser")
 * - PEAR::Auth must be installed in your PEAR directory
 * - Array of setup options must be passed to the constructor.
 *
 * @category authentication
 * @package LiveUser
 * @author  Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser_Auth_PEARAuth extends LiveUser_Auth_Common
{
    /**
     * Contains the PEAR::Auth object.
     *
     * @var    Auth
     * @access private
     */
    var $pearAuth = false;

    /**
     * Contains name of the auth container
     *
     * @var    string
     * @access private
     */
    var $container = false;

    /**
     * Contains array options
     *
     * @var    array
     * @access private
     */
    var $options = false;

    /**
     * Load the storage container
     *
     * @param   array  array containing the configuration.
     * @param string  name of the container that should be used
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function init(&$conf, $containerName)
    {
        parent::init($conf, $containerName);

        if (!is_a($this->pearAuth, 'auth') && $this->container) {
            $pearAuth = &new Auth($this->container, $this->options, '', false);
            if (PEAR::isError($pearAuth)) {
                $this->stack->push(LIVEUSER_ERROR_INIT_ERROR, 'error',
                    array('container' => 'could not connect: '.$pearAuth->getMessage(),
                    'debug' => $pearAuth->getUserInfo()));
                return false;
            }
            $this->pearAuth =& $pearAuth;
        }

        if (!is_a($this->pearAuth, 'auth')) {
            $this->stack->push(LIVEUSER_ERROR_INIT_ERROR, 'error',
                array('container' => 'storage layer configuration missing'));
            return false;
        }

        return true;
    }

    /**
     * Does nothing
     *
     * @return bool true on success or false on failure
     *
     * @access private
     */
    function _updateUserData()
    {
        return true;
    }

    /**
     * Reads user data from the given data source
     * Starts and verifies the PEAR::Auth login process
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
        $this->pearAuth->username = ($auth_user_id !== false) ? $auth_user_id : $handle;
        $this->pearAuth->password = $passwd;
        $this->pearAuth->start();

        if (!$this->pearAuth->getAuth()) {
            return null;
        }

        // User was found, read data into class variables and set return value to true
        $this->propertyValues['auth_user_id'] = $this->pearAuth->getUsername();
        $this->propertyValues['handle'] = $this->pearAuth->getUsername();
        $this->propertyValues['passwd'] = $this->encryptPW($this->pearAuth->password);
        if (!array_key_exists('is_active', $this->tables['users']['fields'])) {
            $this->propertyValues['is_active'] = true;
        }
        if (!array_key_exists('lastlogin', $this->tables['users']['fields'])) {
            $this->propertyValues['lastlogin'] = null;
        }
        return true;
    }
}
?>
