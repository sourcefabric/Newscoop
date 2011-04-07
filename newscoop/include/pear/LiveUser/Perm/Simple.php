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
 * @version CVS: $Id: Simple.php,v 1.41 2006/04/10 14:41:44 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */

/**
 * Base class for permission handling. Provides the simplest
 * set of permission handling features.
 *
 * This class provides a set of functions for implementing a user
 * permission management system on live websites. All authorisation
 * backends/containers must extend this base class.
 *
 * @category  authentication
 * @package   LiveUser
 * @author    Markus Wolff <wolff@21st.de>
 * @author    Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license   http://www.gnu.org/licenses/lgpl.txt
 * @version   Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser_Perm_Simple
{
    /**
     * Unique user ID, used to identify users from the auth container.
     *
     * @var string
     * @access public
     */
    var $perm_user_id = '';

    /**
     * One-dimensional array containing current user's rights (direct and (sub)group).
     * This already includes grouprights and possible overrides by
     * individual right settings.
     *
     * Format: "RightId" => "Level"
     *
     * @var mixed
     * @access public
     */
    var $right_ids = false;

    /**
     * One-dimensional array containing only the individual
     * rights directly assigned to the user.
     *
     * Format: "RightId" => "Level"
     *
     * @var array
     * @access public
     */
    var $user_right_ids = array();

    /**
     * Defines the user type. Depending on the value the user can gain certain
     * rights automatically
     *
     * @var int
     * @access public
     */
    var $perm_type = LIVEUSER_ANONYMOUS_TYPE_ID;

    /**
     * Error stack
     *
     * @var PEAR_ErrorStack
     * @access public
     */
    var $stack = null;

    /**
     * Storage Container
     *
     * @var object
     * @access private
     */
    var $_storage = null;

    /**
     * Class constructor. Feel free to override in backend subclasses.
     */
    function LiveUser_Perm_Simple()
    {
        $this->stack = &PEAR_ErrorStack::singleton('LiveUser');
    }

    /**
     * Load and initialize the storage container.
     *
     * @param array Array with the configuration
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function init(&$conf)
    {
        if (!array_key_exists('storage', $conf)) {
            $this->stack->push(LIVEUSER_ERROR, 'exception',
                array('msg' => 'Missing storage configuration array'));
            return false;
        }

        if (is_array($conf)) {
            $keys = array_keys($conf);
            foreach ($keys as $key) {
                if (isset($this->$key)) {
                    $this->$key =& $conf[$key];
                }
            }
        }

        $this->_storage =& LiveUser::storageFactory($conf['storage']);
        if ($this->_storage === false) {
            end($conf['storage']);
            $key = key($conf['storage']);
            $this->stack->push(LIVEUSER_ERROR, 'exception',
                array('msg' => 'Could not instanciate perm storage container: '.$key));
            return false;
        }

        return true;
    }

    /**
     * Tries to find the user with the given user ID in the permissions
     * container. Will read all permission data and return true on success.
     *
     * @param  string user identifier
     * @param  string name of the auth container
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function mapUser($auth_user_id = null, $containerName = null)
    {
        $result = $this->_storage->mapUser($auth_user_id, $containerName);
        if ($result === false) {
            return false;
        }

        if (is_null($result)) {
            return false;
        }

        $this->perm_user_id = $result['perm_user_id'];
        $this->perm_type    = $result['perm_type'];

        $this->readRights();

        return true;
    }

    /**
     * Reads all rights of current user into a
     * two-dimensional associative array, having the
     * area names as the key of the 1st dimension.
     *
     * @return array requested data or false on failure
     *
     * @access private
     */
    function readRights()
    {
        $this->right_ids = array();
        $result = $this->readUserRights($this->perm_user_id);
        if ($result === false) {
            return false;
        }
        $this->right_ids = $result;
        return $this->right_ids;
    }

    /**
     * Read all the user rights from the storage and puts them in a class
     * member for later retrieval.
     *
     * @param int perm user id
     * @return array requested data or false on failure
     *
     * @access private
     */
    function readUserRights($perm_user_id)
    {
        $this->user_right_ids = array();
        $result = $this->_storage->readUserRights($perm_user_id);
        if ($result === false) {
            return false;
        }
        $this->user_right_ids = $result;
        return $this->user_right_ids;
    }

    /**
     * Checks if the current user has a certain right.
     *
     * If the user is has an "area admin" type he will automatically be
     * awarded the right.
     *
     * @param int Id of the right to check for.
     * @return int level at which the user has the given right or
     *                 false if the user does not have the right.
     *
     * @access public
     */
    function checkRight($right_id)
    {
        // check if the user is above areaadmin
        if (!$right_id || $this->perm_type > LIVEUSER_AREAADMIN_TYPE_ID) {
            return LIVEUSER_MAX_LEVEL;
        // If he does, look for the right in question.
        } elseif (is_array($this->right_ids) && array_key_exists($right_id, $this->right_ids)) {
            // We know the user has the right so the right level will be returned.
            return $this->right_ids[$right_id];
        }
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
        if (isset($this->$what)) {
            $that = $this->$what;
        }
        return $that;
    }

    /**
     * Stores all properties in an array.
     *
     * @param string name of the session in use.
     * @return  array containing the property values
     *
     * @access public
     */
    function freeze($sessionName)
    {
        $propertyValues = array(
            'perm_user_id' => $this->perm_user_id,
            'right_ids' => $this->right_ids,
            'user_right_ids' => $this->user_right_ids,
            'group_right_ids' => $this->group_right_ids,
            'perm_type' => $this->perm_type,
            'group_ids' => $this->group_ids,
        );
        return $this->_storage->freeze($sessionName, $propertyValues);
    }

    /**
     * Reinitializes properties from the storage container.
     *
     * @param string name of the key to use inside the session
     * @param bool always returns true
     *
     * @access public
     */
    function unfreeze($sessionName)
    {
        $propertyValues = $this->_storage->unfreeze($sessionName);
        if ($propertyValues) {
            foreach ($propertyValues as $key => $value) {
                $this->{$key} = $value;
            }
        }
        return true;
    }

    /**
     * Properly disconnect from resources.
     *
     * @return bool true on success and false on failure
     *
     * @access public
     */
    function disconnect()
    {
        $this->_storage->disconnect();
    }
}
?>
