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
 * @category  Authentication
 * @package   LiveUser
 * @author    Markus Wolff <wolff@21st.de>
 * @author    Helgi Þormar Þorbjörnsson <dufuz@php.net>
 * @author    Lukas Smith <smith@pooteeweet.org>
 * @author    Arnaud Limbourg <arnaud@php.net>
 * @author    Pierre-Alain Joye <pajoye@php.net>
 * @author    Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL
 * @version   CVS: $Id: Cache.php 304421 2010-10-15 13:30:56Z clockwerx $
 * @link      http://pear.php.net/LiveUser
 */

/**
 * Require parent class definition.
 */
require_once 'LiveUser/Perm/Storage.php';

/**
 * Cache container for permission handling
 *
 * This is a Cache backend driver for the LiveUser class.
 *
 * @category  Authentication
 * @package   LiveUser
 * @author    Lukas Smith <smith@pooteeweet.org>
 * @author    Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL
 * @version   Release: @package_version@
 * @link      http://pear.php.net/LiveUser
 */
class LiveUser_Perm_Storage_Cache extends LiveUser_Perm_Storage
{
    /**
     * Storage Container
     *
     * @var object
     * @access private
     */
    var $_storage = null;

    /**
     * Initialize the storage container
     *
     * @param array &$storageConf Array with the storage configuration
     * @param array &$confArray   
     *
     * @return bool true on success, false on failure.
     *
     * @access public
     */
    function init(&$storageConf, &$confArray)
    {
        if (!parent::init($storageConf)) {
            return false;
        }

        $this->_storage =& LiveUser::storageFactory($confArray);
        if ($this->_storage === false) {
            $this->stack->push(LIVEUSER_ERROR, 'exception',
                              array('msg' => 'Could not instantiate storage container'));
            return false;
        }

        return true;
    }

    /**
     * map an auth user to a perm user
     *
     * @param int    $auth_user_id
     * @param string $containerName
     *
     * @return array requested data or false on failure
     *
     * @access public
     */
    function mapUser($auth_user_id, $containerName)
    {
        $result = $this->_storage->mapUser($auth_user_id, $containerName);
        if ($result === false) {
            return false;
        }
        //write_into_cache
        return $result;
    }

    /**
     * Reads all rights of current user into a
     * two-dimensional associative array, having the
     * area names as the key of the 1st dimension.
     * Group rights and invididual rights are being merged
     * in the process.
     *
     * @param int $perm_user_id perm user id
     *
     * @return array requested data or false on failure
     * @access public
     */
    function readUserRights($perm_user_id)
    {
        $result = $this->_storage->readUserRights($perm_user_id);
        if ($result === false) {
            return false;
        }
        //write_into_cache
        return $result;
    }

    /**
     * read the areas in which a user is an area admin
     *
     * @param int $perm_user_id perm user id
     *
     * @return array requested data or false on failure
     * @access public
     */
    function readAreaAdminAreas($perm_user_id)
    {
        $result = $this->_storage->readAreaAdminAreas($perm_user_id);
        if ($result === false) {
            return false;
        }
        //write_into_cache
        return $result;
    }

    /**
     * Reads all the group ids in that the user is also a member of
     * (all groups that are subgroups of these are also added recursively)
     *
     * @param int $perm_user_id
     *
     * @return array requested data or false on failure
     * @see    readRights()
     * @access public
     */
    function readGroups($perm_user_id)
    {
        $result = $this->_storage->readGroups($perm_user_id);
        if ($result === false) {
            return false;
        }
        //write_into_cache
        return $result;
    }

    /**
     * Reads the group rights
     * and put them in the array
     *
     * right => 1
     *
     * @param int $group_ids
     *
     * @return array requested data or false on failure
     * @access public
     */
    function readGroupRights($group_ids)
    {
        $result = $this->_storage->readGroupRights($group_ids);
        if ($result === false) {
            return false;
        }
        //write_into_cache
        return $result;
    }

    /**
     * Read the sub groups of the new groups that are not part of the group ids
     *
     * @param array $group_ids   group ids
     * @param array $newGroupIds new group ids
     *
     * @return array requested data or false on failure
     *
     * @access public
     */
    function readSubGroups($group_ids, $newGroupIds)
    {
        $result = $this->_storage->readSubGroups($group_ids, $newGroupIds);
        if ($result === false) {
            return false;
        }
        //write_into_cache
        return $result;
    }

    /**
     * Read out the rights from the userrights or grouprights table
     * that imply other rights along with their level
     *
     * @param array  $rightIds right ids
     * @param string $table    name of the table
     *
     * @return array requested data or false on failure
     *
     * @access public
     */
    function readImplyingRights($rightIds, $table)
    {
        $result = $this->_storage->readImplyingRights($rightIds, $table);
        if ($result === false) {
            return false;
        }
        //write_into_cache
        return $result;
    }

    /**
     * Read out the implied rights with a given level from the implied_rights table
     *
     * @param array  $currentRights current right ids
     * @param string $currentLevel  current level
     *
     * @return array requested data or false on failure
     * @access public
     */
    function readImpliedRights($currentRights, $currentLevel)
    {
        $result = $this->_storage->readImpliedRights($currentRights, $currentLevel);
        if ($result === false) {
            return false;
        }
        //write_into_cache
        return $result;
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
        $this->_storage->disconnect();
    }
}
?>
