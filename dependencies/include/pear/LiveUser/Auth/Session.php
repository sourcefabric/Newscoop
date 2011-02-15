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
 * @version CVS: $Id: Session.php,v 1.29 2006/03/14 13:03:32 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */

require_once 'LiveUser/Auth/Common.php';

/**
 * Session based container for Authentication
 *
 * This is a backend driver for a simple session based anonymous LiveUser class.
 *
 * Requirements:
 * - File "LiveUser.php" (contains the parent class "LiveUser")
 *
 * @category authentication
 * @package LiveUser
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser_Auth_Session extends LiveUser_Auth_Common
{
    /**
     * name of the key containing the Session phrase inside the auth session array
     *
     * @var    string
     * @access public
     */
    var $sessionKey = 'password';

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
     * Compares $passwd with a string inside the $_SESSION array
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
        if (!$auth_user_id) {
            if (!is_null($this->tables['users']['fields']['passwd'])) {
                if (!array_key_exists($this->alias['passwd'], $_SESSION)
                    || $_SESSION[$this->alias['passwd']] !== $passwd
                ) {
                    return false;
                }
            }
            $this->propertyValues = $this->tables['users']['fields'];
            $this->propertyValues['handle']    = $handle;
            $this->propertyValues['passwd']    = $passwd;
            $this->propertyValues['is_active'] = true;
            $this->propertyValues['lastlogin'] = time();
        }

        return true;
    }
}
?>