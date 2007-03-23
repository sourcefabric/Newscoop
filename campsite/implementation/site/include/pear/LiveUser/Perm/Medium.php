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
 * @version CVS: $Id: Medium.php,v 1.27 2006/04/10 14:41:44 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */

/**

 *
 * @package LiveUser
 * @category authentication
 */

/**
 * Require parent class definition.
 */
require_once 'LiveUser/Perm/Simple.php';

/**
 * Medium container for permission handling
 *
 * Medium permission complexity driver for LiveUser.
 *
 * @category authentication
 * @package LiveUser
 * @author   Arnaud Limbourg
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser_Perm_Medium extends LiveUser_Perm_Simple
{
    /**
     * One-dimensional array containing all the groups
     * ids for the actual user.
     *
     * @var array
     * @access public
     */
    var $group_ids = array();

    /**
     * One-dimensional array containing all the groups
     * ids for the actual user without subgroups.
     *
     * @var array
     * @access public
     */
    var $user_group_ids = array();

    /**
     * One-dimensional array containing only the group
     * rights for the actual user.
     *
     * Format: "RightId" => "Level"
     *
     * @var array
     * @access public
     */
    var $group_right_ids = array();

    /**
     * Reads all rights of current user into an
     * associative array.
     * Group rights and invididual rights are being merged
     * in the process.
     *
     * @return bool true on success or false on failure
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

        $result = $this->readGroups($this->perm_user_id);
        if ($result === false) {
            return false;
        }

        $result = $this->readGroupRights($this->group_ids);
        if ($result === false) {
            return false;
        }

        $groupRights = is_array($this->group_right_ids) ? $this->group_right_ids : array();

        // Check if user has individual rights...
        if (is_array($this->user_right_ids)) {
            // Overwrite values from temporary array with values from userrights
            foreach ($this->user_right_ids as $right => $level) {
                if (array_key_exists($right, $groupRights)) {
                    if ($level < 0) {
                        // Revoking rights: A negative value indicates a maximum
                        // possible right level
                        $max_allowed_level = LIVEUSER_MAX_LEVEL + $level;
                        $this->right_ids[$right] = min($groupRights[$right], $max_allowed_level);
                    } elseif ($level > 0) {
                        $this->right_ids[$right] = max($groupRights[$right], $level);
                    } elseif ($level == 0) {
                        unset($this->right_ids[$right]);
                    }
                    unset($groupRights[$right]);
                } elseif ($level < 0) {
                    $this->right_ids[$right] = LIVEUSER_MAX_LEVEL + $level;
                } elseif ($level > 0) {
                    $this->right_ids[$right] = $level;
                } elseif ($level == 0) {
                    unset($this->right_ids[$right]);
                }
            }
        }

        $this->right_ids+= $groupRights;

        return $this->right_ids;
    }

    /**
     * Read all the groups in which the user is a member
     *
     * @param int perm user id
     * @return array requested data or false on failure
     *
     * @access private
     */
    function readGroups($perm_user_id)
    {
        $this->group_ids = $this->user_group_ids = array();

        $result = $this->_storage->readGroups($perm_user_id);
        if ($result === false) {
            return false;
        }

        $this->group_ids = $this->user_group_ids = $result;
        return $this->group_ids;
    }

    /**
     * Reads all rights of the groups into an
     * associative array.
     *
     * @param array group ids
     * @return array requested data or false on failure
     *
     * @access private
     */
    function readGroupRights($group_ids)
    {
        $this->group_right_ids = array();

        if (!is_array($group_ids) || !count($group_ids)) {
            return null;
        }

        $result = $this->_storage->readGroupRights($group_ids);
        if ($result === false) {
            return false;
        }

        $this->group_right_ids = $result;
        return $this->group_right_ids;
    }

    /**
     * Checks if the current user is a member of a certain group
     * If $this->ondemand and $ondemand is true, the groups will be loaded on
     * the fly.
     *
     * @param int Id of the group to check for.
     *
     * @access private
     */
    function checkGroup($group_id)
    {
        if (is_array($this->group_ids)) {
            return in_array($group_id, $this->group_ids);
        }
        return false;
    }
}
?>