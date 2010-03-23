<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * A framework for authentication and authorization in PHP applications
 *
 * LiveUser_Admin is meant to be used with the LiveUser package.
 * It is composed of all the classes necessary to administrate
 * data used by LiveUser.
 *
 * You'll be able to add/edit/delete/get things like:
 * * Rights
 * * Users
 * * Groups
 * * Areas
 * * Applications
 * * Subgroups
 * * ImpliedRights
 *
 * And all other entities within LiveUser.
 *
 * At the moment we support the following storage containers:
 * * DB
 * * MDB
 * * MDB2
 *
 * But it takes no time to write up your own storage container,
 * so if you like to use native mysql functions straight, then it's possible
 * to do so in under a hour!
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
 * @package LiveUser_Admin
 * @author  Markus Wolff <wolff@21st.de>
 * @author  Helgi Þormar Þorbjörnsson <dufuz@php.net>
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @author  Arnaud Limbourg <arnaud@php.net>
 * @author  Christian Dickmann <dickmann@php.net>
 * @author  Matt Scifo <mscifo@php.net>
 * @author  Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version CVS: $Id: Admin.php,v 1.67 2006/08/07 20:38:24 lsmith Exp $
 * @link http://pear.php.net/LiveUser_Admin
 */

require_once 'LiveUser.php';
require_once 'LiveUser/Admin/Storage.php';

/**#@+
 * Error related constants definition
 *
 * @var int
 */
define('LIVEUSER_ADMIN_ERROR',                  -1);
define('LIVEUSER_ADMIN_ERROR_FILTER',           -2);
define('LIVEUSER_ADMIN_ERROR_DATA',             -3);
define('LIVEUSER_ADMIN_ERROR_QUERY_BUILDER',    -4);
define('LIVEUSER_ADMIN_ERROR_ALREADY_ASSIGNED', -5);
define('LIVEUSER_ADMIN_ERROR_NOT_SUPPORTED',    -6);
/**#@-*/

/**
 * A unified admin class
 *
 * Simple usage:
 *
 * <code>
 * $admin = LiveUser_Admin::factory($conf);
 * $filters = array(
 *     'perm_user_id' => '3'
 * );
 * $found = $admin->getUsers($filters);
 *
 * if ($found) {
 *  var_dump($admin->perm->getRights());
 * }
 * </code>
 *
 * @see     LiveUser::factory()
 *
 * @category authentication
 * @package LiveUser_Admin
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @author  Arnaud Limbourg <arnaud@php.net>
 * @author  Helgi Þormar Þorbjörnsson <dufuz@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser_Admin
 */
class LiveUser_Admin
{
     /**
      * Name of the current selected auth container
      *
      * @var    string
      * @access public
      */
     var $authContainerName;

    /**
     * Array containing the auth objects.
     *
     * @var    array
     * @access private
     */
    var $_authContainers = array();

    /**
     * Admin perm object
     *
     * @var    LiveUser_Admin_Perm_Simple
     * @access public
     */
    var $perm = null;

    /**
     * Auth admin object
     *
     * @var    LiveUser_Admin_Auth_Common
     * @access public
     */
    var $auth = null;

    /**
     * Configuration array
     *
     * @var    array
     * @access private
     */
     var $_conf = null;

    /**
     * Error codes to message mapping array
     *
     * @var    array
     * @access private
     */
    var $_errorMessages = array(
        LIVEUSER_ADMIN_ERROR                  => 'An error occurred %msg%',
        LIVEUSER_ADMIN_ERROR_FILTER           => 'There\'s something obscure with the filter array, key %key%',
        LIVEUSER_ADMIN_ERROR_DATA             => 'There\'s something obscure with the data array, key %key%',
        LIVEUSER_ADMIN_ERROR_QUERY_BUILDER    => 'Couldn\'t create the query, reason: %reason%',
        LIVEUSER_ADMIN_ERROR_ALREADY_ASSIGNED => 'That given %field1% has already been assigned to %field2%',
        LIVEUSER_ADMIN_ERROR_NOT_SUPPORTED    => 'This method is not supported'
    );

    /**
     * PEAR::Log object used for error logging by ErrorStack
     *
     * @var    Log
     * @access public
     */
    var $log = null;

    /**
     *
     * @param bool|log boolean value to denote if the debug mode should be
       enabled, or instance of a PEAR_ErrorStack compatible Log object
     * @return LiveUser_Admin
     *
     * @access public
     * @see init
     */
    function LiveUser_Admin(&$debug)
    {
        $this->stack = &PEAR_ErrorStack::singleton('LiveUser_Admin');

        if ($debug) {
            $log =& LiveUser::PEARLogFactory($debug);
            if ($log) {
                $this->log =& $log;
                $this->stack->setLogger($this->log);
            }
        }

        $this->stack->setErrorMessageTemplate($this->_errorMessages);
    }

    /**
     *
     * @param array configuration array
     * @return LiveUser_Admin|bool
     *
     * @access public
     * @see init
     */
    function &factory(&$conf)
    {
        $debug = false;
        if (array_key_exists('debug', $conf)) {
            $debug =& $conf['debug'];
        }

        $obj = &new LiveUser_Admin($debug);

        if (is_array($conf)) {
            $obj->_conf =& $conf;
        }

        return $obj;
    }

    /**
     *
     * @param array configuration array
     * @return LiveUser_Admin|bool
     *
     * @access public
     * @see factory
     */
    function &singleton(&$conf)
    {
        static $instance;

        if (!isset($instance)) {
            if (!$conf) {
                return false;
            }
            $obj = &LiveUser_Admin::factory($conf);
            $instance =& $obj;
        }

        return $instance;
    }

    /**
     * Sets the current auth container to the one with the given auth container name
     *
     * Upon success it will return the auth instance. You can then
     * access the auth backend container by using the
     * auth property of this class or the auth object directly
     *
     * e.g.: $admin->auth->addUser(); or $auth->addUser();
     *
     * @param  string auth container name
     * @return LiveUser_Admin_Auth_Common|bool auth instance upon success, false otherwise
     *
     * @access public
     */
    function &setAdminAuthContainer($authName)
    {
        if (!array_key_exists($authName, $this->_authContainers)
            || !is_object($this->_authContainers[$authName])
        ) {
            if (!isset($this->_conf['authContainers'][$authName])) {
                $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                    array('msg' => 'Could not create auth container instance'));
                $result = false;
                return $result;
            }
            $auth = &LiveUser::authFactory(
                $this->_conf['authContainers'][$authName],
                $authName,
                'LiveUser_Admin_'
            );
            if ($auth === false) {
                $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                    array('msg' => 'Could not instanciate auth container: '.$authName));
                return $auth;
            }
            $this->_authContainers[$authName] = &$auth;
        }
        $this->authContainerName = $authName;
        $this->auth = &$this->_authContainers[$authName];
        return $this->auth;
    }

    /**
     * Sets the perm container
     *
     * Upon success it will return a perm instance. You can then
     * access the perm backend container by using the
     * perm properties of this class or the perm object directly.
     *
     * e.g.: $admin->perm->addUser(); or $perm->addUser();
     *
     * @return LiveUser_Admin_Perm_Simple|bool auth instance upon success, false otherwise
     *
     * @access public
     */
    function &setAdminPermContainer()
    {
        if (!array_key_exists('permContainer', $this->_conf)) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Could not create perm container instance'));
            $result = false;
            return $result;
        }

        $perm = &LiveUser::permFactory($this->_conf['permContainer'], 'LiveUser_Admin_');
        if ($perm === false) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Could not instanciate perm container of type: '.$this->_conf['permContainer']['type']));
            return $perm;
        }
        $this->perm = &$perm;

        return $this->perm;
    }

    /**
     * Setup backend container.
     *
     * Upon success it will return true. You can then
     * access the backend container by using the auth
     * and perm properties of this class.
     *
     * e.g.: $admin->perm->getUsers();
     *
     * @param int user auth id
     * @param  string auth container name
     * @return bool true upon success, false otherwise
     *
     * @access public
     */
    function init($authUserId = null, $authName = null)
    {
        if (!is_array($this->_conf)) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Missing configuration array'));
            return false;
        }

        if (is_null($authName)) {
            if (is_null($authUserId)) {
                reset($this->_conf['authContainers']);
                $authName = key($this->_conf['authContainers']);
            } else {
                foreach ($this->_conf['authContainers'] as $key => $value) {
                    if (!isset($this->_authContainers[$key])
                        || !is_object($this->_authContainers[$key])
                    ) {
                        $auth = &LiveUser::authFactory($value, $key, 'LiveUser_Admin_');
                        if ($auth === false) {
                            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                                array('msg' => 'Could not instanciate auth container: '.$key));
                            return $auth;
                        }
                        $this->_authContainers[$key] =& $auth;
                    }

                    if (!is_null($authUserId)) {
                        $match = $this->_authContainers[$key]->getUsers(
                            array('filters' => array('auth_user_id' => $authUserId))
                        );
                        if (is_array($match) && count($match) > 0) {
                            $authName = $key;
                            break;
                        }
                    }
                }
            }
            if (!isset($authName)) {
                $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                    array('msg' => 'Could not determine what auth container to use'));
                return false;
            }
        }

        if (!$this->setAdminAuthContainer($authName)) {
            return false;
        }

        if (!isset($this->perm) || !is_object($this->perm)) {
            if (!$this->setAdminPermContainer()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add a user to both containers.
     *
     * @param  array auth user data and perm type
     * @return int|bool perm user id or false
     *
     * @access public
     */
    function addUser($data)
    {
        if (!is_object($this->auth) || !is_object($this->perm)) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Perm and/or Auth container not set.'));
            return false;
        }

        if (array_key_exists('perm_type', $data)) {
            $type = $data['perm_type'];
            unset($data['perm_type']);
        } else {
            $type = LIVEUSER_USER_TYPE_ID;
        }

        $authUserId = $this->auth->addUser($data);
        if (!$authUserId) {
            return false;
        }

        $data = array(
            'auth_user_id' => $authUserId,
            'auth_container_name' => $this->authContainerName,
            'perm_type' => $type
        );
        return $this->perm->addUser($data);
    }

    /**
     * Changes user data for both containers.
     *
     * @param  array auth user data and perm type
     * @param int permission user id
     * @return int|bool affected rows on success or false otherwise
     *
     * @access public
     */
    function updateUser($data, $permUserId)
    {
        if (!is_object($this->auth) || !is_object($this->perm)) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Perm and/or Auth container not set.'));
            return false;
        }

        $permData = $this->perm->getUsers(
            array(
                'fields' => array('auth_user_id', 'auth_container_name'),
                'filters' => array('perm_user_id' => $permUserId),
                'select' => 'row',
            )
         );

        if (!$permData) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Could not find user in the permission backend'));
            return false;
        }

        $updateData = array();
        if (array_key_exists('perm_type', $data)) {
            $updateData['perm_type'] = $data['perm_type'];
            unset($data['perm_type']);
        }

        $this->setAdminAuthContainer($permData['auth_container_name']);
        $filters = array('auth_user_id' => $permData['auth_user_id']);
        $result = $this->auth->updateUser($data, $filters);

        if ($result === false) {
            return false;
        }

        if (array_key_exists('auth_user_id', $data)
            && $permData['auth_user_id'] != $data['auth_user_id']
        ) {
            $updateData['auth_user_id'] = $data['auth_user_id'];
        }
        if (empty($updateData)) {
            return $result;
        }

        $filters = array('perm_user_id' => $permUserId);
        return $this->perm->updateUser($updateData, $filters);
    }

    /**
     * Removes user from both Perm and Auth containers
     *
     * @param int Perm ID
     * @return int|bool affected rows on success or false otherwise
     *
     * @access public
     */
    function removeUser($permUserId)
    {
        if (!is_object($this->auth) || !is_object($this->perm)) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Perm and/or Auth container not set.'));
            return false;
        }

        $permData = $this->perm->getUsers(
            array(
                'fields' => array('auth_user_id', 'auth_container_name'),
                'filters' => array('perm_user_id' => $permUserId),
                'select' => 'row',
            )
         );

        if (!$permData) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Could not find user in the permission backend'));
            return false;
        }

        $filters = array('perm_user_id' => $permUserId);
        $result = $this->perm->removeUser($filters);

        if ($result === false) {
            return false;
        }

        $this->setAdminAuthContainer($permData['auth_container_name']);
        $filters = array('auth_user_id' => $permData['auth_user_id']);
        return $this->auth->removeUser($filters);
    }

    /**
     * Finds and gets full userinfo by filtering inside the given container
     * Note that this method is not particularily efficient, as it fetches
     * the data in the primary container in a single call, but requires one call
     * to the secondary container for every user returned from the primary container
     *
     * @param  array params (as for getUsers()
     *          with an additional optional key 'container' 'perm' (default) or
                'auth' to determine the primary and secondary container.
                data is first fetched from the primary container and then
                combined with data from the secondary container if available
     * @return array|bool array with userinfo if found on success or false otherwise
     *
     * @access public
     */
    function getUsers($params = array())
    {
        $params = LiveUser_Admin_Storage::setSelectDefaultParams($params);

        if ($params['select'] != 'row' && $params['select'] != 'all') {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Select must be "row" or "all"'));
            return false;
        }

        if (array_key_exists('container', $params)
            && $params['container'] == 'auth'
        ) {
            return $this->_getUsersByAuth($params);
        }
        return $this->_getUsersByPerm($params);
    }

    /**
     * Finds and gets full userinfo by filtering inside the perm container
     *
     * @param  array perm params (as for getUsers() from the perm container
     * @return array|bool Array with userinfo if found on success or false otherwise
     *
     * @access private
     */
    function _getUsersByPerm($permParams = array())
    {
        if (!is_object($this->perm)) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Perm container not set.'));
            return false;
        }

        $first = ($permParams['select'] == 'row');
        $permUsers = $this->perm->getUsers($permParams);
        if (!$permUsers) {
            return $permUsers;
        }

        if ($first) {
            $permUsers = array($permUsers);
        }

        $users = array();
        foreach ($permUsers as $permData) {
            if (!$this->setAdminAuthContainer($permData['auth_container_name'])) {
                $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                    array('msg' => 'Auth container could not be set.'));
                return false;
            }

            $authParams = array(
                'filters' => array('auth_user_id' => $permData['auth_user_id']),
                'select' => 'row',
            );
            $authData = $this->auth->getUsers($authParams);
            if (!$authData) {
                continue;
            }

            if ($first) {
                return LiveUser::arrayMergeClobber($permData, $authData);
            }
            $users[] = LiveUser::arrayMergeClobber($permData, $authData);
        }

        return $users;
    }

    /**
     * Finds and gets full userinfo by filtering inside the auth container
     *
     * @param  array auth params (as for getUsers() from the auth container
     * @return array|bool Array with userinfo if found on success or false otherwise
     *
     * @access private
     */
    function _getUsersByAuth($authParams = array())
    {
        if (!is_object($this->auth) || !is_object($this->perm)) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Perm and/or Auth container not set.'));
            return false;
        }

        $first = ($authParams['select'] == 'row');
        $authUsers = $this->auth->getUsers($authParams);
        if (!$authUsers) {
            return $authUsers;
        }

        if ($first) {
            $authUsers = array($authUsers);
        }

        $users = array();
        foreach ($authUsers as $authData) {
            $permParams = array(
                'filters' => array(
                    'auth_user_id' => $authData['auth_user_id'],
                    'auth_container_name' => $this->authContainerName,
                ),
                'select' => 'row',
            );
            $permData = $this->perm->getUsers($permParams);
            if (!$permData) {
                continue;
            }

            if ($first) {
                return LiveUser::arrayMergeClobber($authData, $permData);
            }
            $users[] = LiveUser::arrayMergeClobber($authData, $permData);
        }

        return $users;
    }

    /**
     * Wrapper method to get the Error Stack
     *
     * @return array  an array of the errors
     *
     * @access public
     */
    function getErrors()
    {
        if (is_object($this->stack)) {
            return $this->stack->getErrors();
        }
        return false;
    }

    /**
     * Calls a method using the __call() magic method on perm or auth
     *
     * @param string method name
     * @param array  arguments
     * @return mixed returned value
     *
     * @access private
     */
    function __call($method, $params)
    {
        if (is_object($this->perm) && method_exists($this->perm, $method)) {
            return call_user_func_array(array(&$this->perm, $method), $params);
        }
        if (is_object($this->auth) && method_exists($this->auth, $method)) {
            return call_user_func_array(array(&$this->auth, $method), $params);
        }
        trigger_error(sprintf('Call to undefined function: %s::%s().', get_class($this), $method), E_USER_ERROR);
    }
}
