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
 * @version CVS: $Id: Storage.php,v 1.18 2006/03/14 13:10:04 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */

/**
 * Abstraction class for all the storage containers
 *
 * @category authentication
 * @package LiveUser
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @author  Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser_Perm_Storage
{
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
     * Constructor
     *
     * @access protected
     * @param  mixed      configuration array
     * @return void
     */
    function LiveUser_Perm_Storage()
    {
        $this->stack = &PEAR_ErrorStack::singleton('LiveUser');
    }

    /**
     * Initialize the storage container
     *
     * @param array Array with the storage configuration
     * @return bool true on success, false on failure.
     *
     * @access public
     */
    function init(&$storageConf)
    {
        if (is_array($storageConf)) {
            $keys = array_keys($storageConf);
            foreach ($keys as $key) {
                if (isset($this->$key)) {
                    $this->$key =& $storageConf[$key];
                }
            }
        }

        require_once 'LiveUser/Perm/Storage/Globals.php';
        if (empty($this->tables)) {
            $this->tables = $GLOBALS['_LiveUser']['perm']['tables'];
        } else {
            $this->tables = LiveUser::arrayMergeClobber($GLOBALS['_LiveUser']['perm']['tables'], $this->tables);
        }
        if (empty($this->fields)) {
            $this->fields = $GLOBALS['_LiveUser']['perm']['fields'];
        } else {
            $this->fields = LiveUser::arrayMergeClobber($GLOBALS['_LiveUser']['perm']['fields'], $this->fields);
        }
        if (empty($this->alias)) {
            $this->alias = $GLOBALS['_LiveUser']['perm']['alias'];
        } else {
            $this->alias = LiveUser::arrayMergeClobber($GLOBALS['_LiveUser']['perm']['alias'], $this->alias);
        }

        return true;
    }

    /**
     * map an auth user to a perm user
     *
     * @param int $auth_user_id
     * @param string $containerName
     * @return array requested data or false on failure
     *
     * @access public
     */
    function mapUser($auth_user_id, $containerName)
    {
        $this->stack->push(LIVEUSER_ERROR, 'error', array(),
            __METHOD__.' is not implemented');
        return false;
    }

    /**
     * Reads all rights of current user into a
     * two-dimensional associative array, having the
     * area names as the key of the 1st dimension.
     * Group rights and invididual rights are being merged
     * in the process.
     *
     * @param int perm user id
     * @return array requested data or false on failure
     *
     * @access public
     */
    function readUserRights($perm_user_id)
    {
        $this->stack->push(LIVEUSER_ERROR, 'error', array(),
            __METHOD__.' is not implemented');
        return false;
    }

    /**
     * read the areas in which a user is an area admin
     *
     * @param int perm user id
     * @return array requested data or false on failure
     *
     * @access public
     */
    function readAreaAdminAreas($perm_user_id)
    {
        $this->stack->push(LIVEUSER_ERROR, 'error', array(),
            __METHOD__.' is not implemented');
        return false;
    }

    /**
     * Reads all the group ids in that the user is also a member of
     * (all groups that are subgroups of these are also added recursively)
     *
     * @param int perm user id
     * @return array requested data or false on failure
     *
     * @see    readRights()
     * @access public
     */
    function readGroups($perm_user_id)
    {
        $this->stack->push(LIVEUSER_ERROR, 'error', array(),
            __METHOD__.' is not implemented');
        return false;
    }

    /**
     * Reads the group rights
     * and put them in the array
     *
     * right => 1
     *
     * @param int group ids
     * @return array requested data or false on failure
     *
     * @access public
     */
    function readGroupRights($group_ids)
    {
        $this->stack->push(LIVEUSER_ERROR, 'error', array(),
            __METHOD__.' is not implemented');
        return false;
    }

    /**
     * Read the sub groups of the new groups that are not part of the group ids
     *
     * @param array group ids
     * @param array new group ids
     * @return array requested data or false on failure
     *
     * @access public
     */
    function readSubGroups($group_ids, $newGroupIds)
    {
        $this->stack->push(LIVEUSER_ERROR, 'error', array(),
            __METHOD__.' is not implemented');
        return false;
    }

    /**
     * Read out the rights from the userrights or grouprights table
     * that imply other rights along with their level
     *
     * @param array right ids
     * @param string name of the table
     * @return array requested data or false on failure
     *
     * @access public
     */
    function readImplyingRights($rightIds, $table)
    {
        $this->stack->push(LIVEUSER_ERROR, 'error', array(),
            __METHOD__.' is not implemented');
        return false;
    }

    /**
    * Read out the implied rights with a given level from the implied_rights table
    *
    * @param array current right ids
    * @param string current level
     * @return array requested data or false on failure
    *
    * @access public
    */
    function readImpliedRights($currentRights, $currentLevel)
    {
        $this->stack->push(LIVEUSER_ERROR, 'error', array(),
            __METHOD__.' is not implemented');
        return false;
    }

    /**
     * store all properties in the session and return them as an array
     *
     * @param string name of the key to use inside the session
     * @param array property values
     * @return array containing the property values
     *
     * @access public
     */
    function freeze($sessionName, $propertyValues)
    {
        $_SESSION[$sessionName]['perm'] = $propertyValues;
        return $propertyValues;
    }

    /**
     * Reinitializes properties
     *
     * @param string name of the key to use inside the session
     * @return array
     *
     * @access public
     */
    function unfreeze($sessionName)
    {
        return (array_key_exists('perm', $_SESSION[$sessionName])
            && is_array($_SESSION[$sessionName]['perm']))
                ? $_SESSION[$sessionName]['perm'] : array();
    }

    /**
     * properly disconnect from resources
     *
     * @return bool true on success and false on failure
     *
     * @access public
     */
    function disconnect()
    {
    }
}
?>