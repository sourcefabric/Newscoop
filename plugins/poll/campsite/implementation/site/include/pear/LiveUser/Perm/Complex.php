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
 * @version CVS: $Id: Complex.php,v 1.27 2006/04/10 14:41:44 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */

/**
 * Require parent class definition.
 */
require_once 'LiveUser/Perm/Medium.php';

/**
 * Complex container for permission handling
 *
 * Complex permission complexity driver for LiveUser.
 *
 * @category authentication
 * @package LiveUser
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @author  Bjoern Kraus <krausbn@php.net>
 * @version $Id: Complex.php,v 1.27 2006/04/10 14:41:44 lsmith Exp $
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser_Perm_Complex extends LiveUser_Perm_Medium
{
    /**
     * Reads all individual implied rights of current user into
     * an array of this format:
     * RightName -> Value
     *
     * @param array $rightIds
     * @param string $table
     * @return array with rightIds as key and level as value
     *
     * @access private
     */
    function _readImpliedRights($rightIds, $table)
    {
        if (!is_array($rightIds) || !count($rightIds)) {
            return null;
        }

        $result = $this->_storage->readImplyingRights($rightIds, $table);
        if ($result === false) {
            return false;
        }

        $queue = $result;
        while (count($queue)) {
            $currentRights = reset($queue);
            $currentLevel = key($queue);
            unset($queue[$currentLevel]);

            $result = $this->_storage->readImpliedRights($currentRights, $currentLevel);
            if (!is_array($result)) {
                return false;
            }
            foreach ($result as $val) {
                // only store the implied right if the right wasn't stored before
                // or if the level is higher
                if (!array_key_exists($val['right_id'], $rightIds)
                    || $rightIds[$val['right_id']] < $val['right_level']
                ) {
                    $rightIds[$val['right_id']] = $val['right_level'];
                    if ($val['has_implied']) {
                        $queue[$val['right_level']][] = $val['right_id'];
                    }
                }
            }
        }
        return $rightIds;
    }

    /**
     * Reads all individual rights of current user into
     * an array of this format:
     * RightName -> Value
     *
     * @param int perm user id
     * @see    readRights()
     * @return array requested data or false on failure
     *
     * @access private
     */
    function readUserRights($perm_user_id)
    {
        $result = parent::readUserRights($perm_user_id);
         if ($result === false) {
            return false;
         }

        if ($this->perm_type == LIVEUSER_AREAADMIN_TYPE_ID) {
            $result = $this->readAreaAdminAreas($this->perm_user_id);
            if ($result === false) {
               return false;
            }

            if (is_array($this->area_admin_areas)) {
                if (is_array($this->user_right_ids)) {
                    $this->user_right_ids = $this->area_admin_areas + $this->user_right_ids;
                } else {
                    $this->user_right_ids = $this->area_admin_areas;
                }
            }
        }

        $this->user_right_ids = $this->_readImpliedRights($this->user_right_ids, 'user');

        return $this->user_right_ids;
    }

    /**
     * Reads all the group ids in that the user is also a member of
     * (all groups that are subgroups of these are also added recursively)
     *
     * @param int perm user id
     * @see    readRights()
     * @return array requested data or false on failure
     *
     * @access private
     */
    function readGroups($perm_user_id)
    {
        $result = parent::readGroups($perm_user_id);

        // get all subgroups recursively
        while (count($result)) {
            $result = $this->readSubGroups($this->group_ids, $result);
            if (is_array($result)) {
                $this->group_ids = array_merge($result, $this->group_ids);
            }
        }
        return $this->group_ids;
    }

    /**
     * Read the sub groups of the groups where the user is a member in
     *
     * @param array group ids
     * @param array new group ids
     * @return array requested data or false on failure
     *
     * @access private
     */
    function readSubGroups($group_ids, $newGroupIds)
    {
        $result = $this->_storage->readSubGroups($group_ids, $newGroupIds);
        if ($result === false) {
            return false;
        }
        return $result;
    }

    /**
     * Reads all individual rights of current user into
     * a two-dimensional array of this format:
     * "GroupName" => "RightName" -> "Level"
     *
     * @param   array id's for the groups that rights will be read from
     * @see    readRights()
     * @return array requested data or false on failure
     *
      * @access private
     */
    function readGroupRights($group_ids)
    {
        $group_right_ids = parent::readGroupRights($group_ids);
        $this->group_right_ids = $this->_readImpliedRights($group_right_ids, 'group');

        return $this->group_right_ids;
    }

    /**
     * Checks if the current user has a certain right in a
     * given area at the necessary level.
     *
     * Level 1: requires that owner_user_id matches $this->perm_user_id
     * Level 2: requires that the $owner_group_id matches the id one of
     *          the (sub)groups that $this->perm_user_id is a member of
     *          or requires that the $owner_user_id matches a perm_user_id of
     *          a member of one of $this->perm_user_id's (sub)groups
     * Level 3: no requirements
     *
     * Important note:
     *          Every ressource MAY be owned by a user and/or by a group.
     *          Therefore, $owner_user_id and/or $owner_group_id can
     *          either be an integer or null.
     *
     * @see    checkRightLevel()
     * @param int       Level value as returned by checkRight().
     * @param int|array Id or array of Ids of the owner of the
                        ressource for which the right is requested.
     * @param int|array Id or array of Ids of the group of the
     *                  ressource for which the right is requested.
     * @return bool level if the level is sufficient to grant access else false.
     *
     * @access public
     */
    function checkLevel($level, $owner_user_id, $owner_group_id)
    {
        // level above 0
        if ($level <= 0) {
            return false;
        }
        // highest level (that is level 3) or no owner id's passed
        if ($level == LIVEUSER_MAX_LEVEL
            || (is_null($owner_user_id) && is_null($owner_group_id))
        ) {
            return $level;
        }
        // level 1 or higher
        if ((!is_array($owner_user_id) && $this->perm_user_id == $owner_user_id)
            || is_array($owner_user_id) && in_array($this->perm_user_id, $owner_user_id)
        ) {
            return $level;
        // level 2 or higher
        }
        if ($level >= 2) {
            // check if the ressource is owned by a (sub)group
            // that the user is part of
            if (is_array($owner_group_id)) {
                if (count(array_intersect($owner_group_id, $this->group_ids))) {
                    return $level;
                }
            } elseif (in_array($owner_group_id, $this->group_ids)) {
                return $level;
            }
        }
        return false;
    }

    /**
     * Read all the areas in which the user is an area admin
     *
     * @param int perm user id
     * @return array requested data or false on failure
     *
     * @access private
     */
    function readAreaAdminAreas($perm_user_id)
    {
        $result = $this->_storage->readAreaAdminAreas($perm_user_id);
        if ($result === false) {
            return false;
        }

        $this->area_admin_areas = $result;
        return $this->area_admin_areas;
    }
}
?>
