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
 * @author Markus Wolff <wolff@21st.de>
 * @author  Helgi Þormar Þorbjörnsson <dufuz@php.net>
 * @author Lukas Smith <smith@pooteeweet.org>
 * @author  Arnaud Limbourg <arnaud@php.net>
 * @author Christian Dickmann <dickmann@php.net>
 * @author Matt Scifo <mscifo@php.net>
 * @author Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version CVS: $Id: Complex.php,v 1.99 2006/04/19 08:41:06 lsmith Exp $
 * @link http://pear.php.net/LiveUser_Admin
 */

/**
 * Require the parent class definition
 */
require_once 'LiveUser/Admin/Perm/Medium.php';

/**
 * Complex permission administration class that extends the Medium class with the
 * ability to manage subgroups, implied rights and area admins
 *
 * This class provides a set of functions for implementing a user
 * permission management system on live websites. All authorisation
 * backends/containers must be extensions of this base class.
 *
 * @category authentication
 * @package LiveUser_Admin
 * @author  Christian Dickmann <dickmann@php.net>
 * @author  Markus Wolff <wolff@21st.de>
 * @author  Matt Scifo <mscifo@php.net>
 * @author Helgi ï¿½rmar ï¿½rbjï¿½nsson <dufuz@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser_Admin
 */
class LiveUser_Admin_Perm_Complex extends LiveUser_Admin_Perm_Medium
{
    /**
     * Constructor
     *
     * @return void
     *
     * @access protected
     */
    function LiveUser_Admin_Perm_Complex()
    {
        $this->LiveUser_Admin_Perm_Medium();
        $this->selectable_tables['getRights'][] = 'right_implied';
        $this->selectable_tables['getAreas'][] = 'area_admin_areas';
        $this->selectable_tables['getGroups'][] = 'group_subgroups';
    }

    /**
     * Assign subgroup to parent group.
     *
     * First checks if groupId and subgroupId are the same then if
     * the child group is already assigned to the parent group and last if
     * the child group does have a parent group already assigned to it.
     * (Just to difference between what kinda error was hit)
     *
     * If so it returns false and pushes the error into stack.
     *
     * The expected parameter array is of the form
     * <code>
     * $lua->perm->assignSubGroup(
     *     array('group_id' => 'id', 'subgroup_id' => 'id')
     * );
     * </code>
     *
     * @param array containing the subgroup_id and group_id
     * @return bool false on error, true on success
     *
     * @access public
     */
    function assignSubGroup($data)
    {
        // Checking if the supplied data is valid:
        // you can't assign a group as it's own subgroup
        if ($data['subgroup_id'] == $data['group_id']) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Parent group id is the same as the subgroup id')
            );
            return false;
        }

        // Check if the group is already assigned to this group (retrieve the group_id).
        // It also checks if the group exists (if not: return false).
        $filter = array('subgroup_id' => $data['subgroup_id']);
        $result = $this->_storage->selectCount('group_subgroups', 'group_id', $filter);
        if ($result === false) {
            return false;
        }

         // Do the actual check
        if ($result == $data['group_id']) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'This child group is already a Parent of this group')
            );
            return false;
        }

        // Assign the group as a subgroup.
        $result = $this->_storage->insert('group_subgroups', $data);
        // todo: notify observer
        return $result;
    }

    /**
     * Unassign subgroup(s) from group(s)
     *
     * @param array key values pairs (value may be a string or an array)
     *                      This will construct the WHERE clause of your update
     *                      Be careful, if you leave this blank no WHERE clause
     *                      will be used and all groups will be affected by the remove
     * @return int|bool false on error, the affected rows on success
     *
     * @access public
     */
    function unassignSubGroup($filters)
    {
        // Perform a delete on the group_subgroups table (no data sanity checks required).
        $result = $this->_storage->delete('group_subgroups', $filters);
        // todo: notify observer
        return $result;
    }

    /**
     * Imply Right
     *
     * @param array containing the implied_right_id and right_id
     * @return bool false on error, true on success
     *
     * @access public
     */
    function implyRight($data)
    {
        // Check if the implied_right_id is the same as the right_id (you can't imply itself)
        if (array_key_exists('right_id', $data)
            && array_key_exists('implied_right_id', $data)
            && $data['implied_right_id'] == $data['right_id']
        ) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Right id is the same as the implied right id')
            );
            return false;
        }

        // Prepare the params for a check on the implied_right existing and already being implied
        $params = array(
            'fields' => array(
                'right_id'
            ),
            'filters' => array(
                'implied_right_id' => $data['implied_right_id'],
                'right_id' => $data['right_id']
            )
        );

        $result = $this->_getImpliedRight($params);
        if ($result === false) {
            return false;
        }

        // Check if the implied right is already implied
        if (!empty($result)) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'This implied right is already implied from this right')
            );
            return false;
        }

        // It all checks out, imply the right. It something goes wrong with the insert return false.
        $result = $this->_storage->insert('right_implied', $data);
        if ($result === false) {
            return false;
        }

        // Update the implied status of this right, the has_implied column in the database should now report 1
        $filter = array('right_id' => $data['right_id']);
        $this->_updateImpliedStatus($filter);

        // todo: notify observer
        return $result;
    }

    /**
     * Unimply right(s)
     *
     * @param array key values pairs (value may be a string or an array)
     *                      This will construct the WHERE clause of your update
     *                      Be careful, if you leave this blank no WHERE clause
     *                      will be used and all groups will be affected by the remove
     * @param bool determines if the implied rights field in the rights table
     *                should be updated or not
     * @return int|bool false on error, the affected rows on success
     *
     * @access public
     */
    function unimplyRight($filters, $update = true)
    {
        // Generate the implied_filters. If an error occures, return it (being 0 or false).
        $implied_filters = $this->_makeRemoveFilter($filters, 'implied_right_id', 'getRights');
        if (!$implied_filters) {
            return $implied_filters;
        }

        if ($update) {
            // Generate the rights_filter for updating the has_implied column in the rights table
            $right_filters = $this->_makeRemoveFilter($filters, 'right_id', 'getRights');
            if (!$right_filters) {
                return $right_filters;
            }
        }

        // Unimply the right (perform a delete on the right_implied table) based on the implied_filters
        $result = $this->_storage->delete('right_implied', $implied_filters);
        if ($result === false) {
            return false;
        }

        if ($update) {
            // Update the has_implied status in the rights table, based on the right_filters
            $this->_updateImpliedStatus($right_filters);
        }

        // todo: notify observer
        return $result;
    }

    /**
     * Add Area Admin
     *
     * @param array containing the area_id and perm_user_id
     * @return bool false on error, true on success
     *
     * @access public
     */
    function addAreaAdmin($data)
    {
        // needs more sanity checking, check if the perm_id is really perm_type 3 and so on
        // make sure when removing rights or updating them that if the user goes down
        // below perm_type 3 that a entry from area_admin_areas is removed

        // Prepare the params for retrieving the userinfo (checking if the user actually exists).
        $params = array(
            'fields' => array(
                'perm_type'
            ),
            'filters' => array(
                'perm_user_id' => $data['perm_user_id']
            ),
            'select' => 'one',
        );

        $result = parent::getUsers($params);
        if ($result === false) {
            return false;
        }

        // Check if the user has sufficient rights to become an area admin
        if (!$result || $result < 3) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'The user doesn\'t exist or does not have perm_type >= 3')
            );
            return false;
        }

        // Make the user area admin for this area.
        $result = $this->_storage->insert('area_admin_areas', $data);

        // todo: notify observer
        return $result;
    }

    /**
     * Remove Area Admin(s)
     *
     * @param array key values pairs (value may be a string or an array)
     *                      This will construct the WHERE clause of your update
     *                      Be careful, if you leave this blank no WHERE clause
     *                      will be used and all groups will be affected by the remove
     * @return int|bool false on error, the affected rows on success
     *
     * @access public
     */
    function removeAreaAdmin($filters)
    {
        // Delete the user from the area_admin_areas table based on the filters provided.
        $result = $this->_storage->delete('area_admin_areas', $filters);
        if ($result === false) {
            return false;
        }

        // todo: notify observer
        return $result;
    }

    /**
     * Remove areas and all their relevant relations
     *
     * @param array key values pairs (value may be a string or an array)
     *                      This will construct the WHERE clause of your update
     *                      Be careful, if you leave this blank no WHERE clause
     *                      will be used and all areas will be affected by the remove
     * @return int|bool false on error, the affected rows on success
     *
     * @access public
     */
    function removeArea($filters)
    {
        // Generate the remove filters, return 0 or false immediatly
        $filters = $this->_makeRemoveFilter($filters, 'area_id', 'getAreas');
        if (!$filters) {
            return $filters;
        }

        // Remove the area admin (leave no mess behind in the tables) using the filters (containing only the area_id).
        $result = $this->removeAreaAdmin($filters);
        if ($result === false) {
            return false;
        }

        // Remove the area using the Perm:Simple container.
        $result = parent::removeArea($filters);

        // todo: notify observer
        return $result;
    }

    /**
     * Remove rights and all their relevant relations
     *
     * @param array key values pairs (value may be a string or an array)
     *                      This will construct the WHERE clause of your update
     *                      Be careful, if you leave this blank no WHERE clause
     *                      will be used and all rights will be affected by the remove
     * @return int|bool false on error, the affected rows on success
     *
     * @access public
     */
    function removeRight($filters)
    {
        // First take care of any mess which may possibly be left behind (the implied_rights table).
        $result = $this->unimplyRight($filters, false);
        if ($result === false) {
            return false;
        }

        // Remove the right.
        $result = parent::removeRight($filters);
        if ($result === false) {
            return false;
        }

         // Update the has_implied status (clean up the database so no invalid information is left behind).
        $this->_updateImpliedStatus($filters);

        // todo: notify observer
        return $result;
    }

    /**
     * Get SubGroups
     *
     * @param array containing key-value pairs for:
     *                 'fields'  - ordered array containing the fields to fetch
     *                             if empty all fields from the user table are fetched
     *                 'filters' - key values pairs (value may be a string or an array)
     *                 'orders'  - key value pairs (values 'ASC' or 'DESC')
     *                 'rekey'   - if set to true, returned array will have the
     *                             first column as its first dimension
     *                 'group'   - if set to true and $rekey is set to true, then
     *                             all values with the same first column will be
     *                             wrapped in an array
     *                 'limit'   - number of rows to select
     *                 'offset'  - first row to select
     *                 'select'  - determines what query method to use:
     *                             'one' -> queryOne, 'row' -> queryRow,
     *                             'col' -> queryCol, 'all' ->queryAll (default)
     * @return bool|array false on failure or array with selected data
     *
     * @access private
     */
    function _getSubGroups($params = array())
    {
        // Define the tables which can be included the the query and define the root table.
        $selectable_tables = array('group_subgroups');
        $root_table = 'group_subgroups';

        $data = $this->_makeGet($params, $root_table, $selectable_tables);
        return $data;
    }

    /**
     * Get Implied Rights
     *
     * @param array containing key-value pairs for:
     *                 'fields'  - ordered array containing the fields to fetch
     *                             if empty all fields from the user table are fetched
     *                 'filters' - key values pairs (value may be a string or an array)
     *                 'orders'  - key value pairs (values 'ASC' or 'DESC')
     *                 'rekey'   - if set to true, returned array will have the
     *                             first column as its first dimension
     *                 'group'   - if set to true and $rekey is set to true, then
     *                             all values with the same first column will be
     *                             wrapped in an array
     *                 'limit'   - number of rows to select
     *                 'offset'  - first row to select
     *                 'select'  - determines what query method to use:
     *                             'one' -> queryOne, 'row' -> queryRow,
     *                             'col' -> queryCol, 'all' ->queryAll (default)
     * @return bool|array false on failure or array with selected data
     *
     * @access private
     */
    function _getImpliedRight($params = array())
    {
        // Define the tables which can be included the the query and define the root table.
        $selectable_tables = array('right_implied');
        $root_table = 'right_implied';

        $data = $this->_makeGet($params, $root_table, $selectable_tables);
        return $data;
    }

    /**
     * Remove groups and all their relevant relations
     *
     * @param array key values pairs (value may be a string or an array)
     *                      This will construct the WHERE clause of your update
     *                      Be careful, if you leave this blank no WHERE clause
     *                      will be used and all groups will be affected by the removed
     * @return int|bool false on error, the affected rows on success
     *
     * @access public
     */
    function removeGroup($filters)
    {
        // If recursive is set, prepare a params array, get the subgroups and if there are any
        // do a recursive call (again providing the recursive param, in case there are more subgroups).
        if (array_key_exists('recursive', $filters)) {
            $param = array(
                'fields' => array(
                    'subgroup_id'
                ),
                'filters' => array(
                    'group_id' => $filters['group_id']
                )
            );
            $result = $this->_getSubGroups($param);
            if ($result === false) {
                return false;
            }

            // Loop through the available subgroups and remove them.
            foreach ($result as $subGroupId) {
                $filter = array('group_id' => $subGroupId['subgroup_id'], 'recursive' => true);
                $result = $this->removeGroup($filter);
                if ($result === false) {
                    return false;
                }
            }
            unset($filters['recursive']);
        }

        // Unassign any subgroups that may have been assigned to this group. (clean up the database)
        $result = $this->unassignSubGroup($filters);
        if ($result === false) {
            return false;
        }

        // Remove the group using Perm:Medium.
        return parent::removeGroup($filters);
    }

    /**
     * Updates implied status
     *
     * @param array key values pairs (value may be a string or an array)
     *                      This will construct the WHERE clause of your update
     *                      Be careful, if you leave this blank no WHERE clause
     *                      will be used and all rights will be affected by the update
     * @return bool denotes success or failure
     *
     * @access private
     */
    function _updateImpliedStatus($filters)
    {
        // Prepare the params array for getting the rights which will be updated, based on the provided filters array.
        $params = array(
            'fields' => array('right_id'),
            'filters' => $filters,
            'select' => 'col',
        );

        // Get the rights.
        $rights = $this->getRights($params);
        if ($rights === false) {
            return false;
        }

        // Prepare the filters for fetching the amount of rights.
        $filters = array('right_id' => $rights);

        // Count the rights which are included in the right_implied table.
        $count = $this->_storage->selectCount('right_implied', 'right_id', $filters);
        if ($count === false) {
            return false;
        }

        // Update the has_implied with true of false (if count has a value true, otherwise false)
        $data = array('has_implied' => (bool)$count);

        // Perform the update.
        $result = $this->updateRight($data, $filters);
        if ($result === false) {
            return false;
        }

        // todo: notify observer
        return $result;
    }

    /**
     * Get parent of a subgroup
     *
     * @param Id of the subgroup_id that is used to fetch the parent
     * @return bool|int false on failure or integer with the parent group_id
     *
     * @access public
     */
    function getParentGroup($subGroupId)
    {
        // Prepare the params array for the _getSubGroups().
        $params = array(
            'fields' => array(
                'group_id'
            ),
            'filters' => array(
                'subgroup_id' => $subGroupId
            ),
            'select' => 'one'
        );
        $result = $this->_getSubGroups($params);

        return $result;
    }

    /**
     * Fetches groups
     *
     * @param array containing key-value pairs for:
     *                 'fields'  - ordered array containing the fields to fetch
     *                             if empty all fields from the user table are fetched
     *                 'filters' - key values pairs (value may be a string or an array)
     *                 'orders'  - key value pairs (values 'ASC' or 'DESC')
     *                 'rekey'   - if set to true, returned array will have the
     *                             first column as its first dimension
     *                 'group'   - if set to true and $rekey is set to true, then
     *                             all values with the same first column will be
     *                             wrapped in an array
     *                 'limit'   - number of rows to select
     *                 'offset'  - first row to select
     *                 'select'  - determines what query method to use:
     *                             'one' -> queryOne, 'row' -> queryRow,
     *                             'col' -> queryCol, 'all' ->queryAll (default)
     *                 'selectable_tables' - array list of tables that may be
     *                             joined to in this query, the first element is
     *                             the root table from which the joins are done
     *                 'subgroups' - filter array if all subgroups should be
                                   fetched into a flat array
     *                 'hierarchy' - filter array if all subgroups should be
                                   fetched into a nested array (overwrites 'subgroups')
     *
     *    note that 'hierarchy' requires 'rekey' enabled, 'group' is disabled,
     *    'select' set to 'all' and the first field needs to be 'group_id'
     * @return bool|array false on failure or array with selected data
     *
     * @access public
     */
    function getGroups($params = array())
    {
        if (!array_key_exists('subgroups', $params)
            && !array_key_exists('hierarchy', $params)
        ) {
            // Don't have to deal with subgroups
            return parent::getGroups($params);
        }

        $params = LiveUser_Admin_Storage::setSelectDefaultParams($params);

        if (array_key_exists('hierarchy', $params)) {
            return $this->_getGroupsWithHierarchy($params);
        }

        return $this->_getGroupsWithSubgroups($params);
    }

    /**
     * Fetches groups with their subgroups into a flat structure
     *
     * @param array containing key-value pairs for:
     *                 'fields'  - ordered array containing the fields to fetch
     *                             if empty all fields from the user table are fetched
     *                 'filters' - key values pairs (value may be a string or an array)
     *                 'orders'  - key value pairs (values 'ASC' or 'DESC')
     *                 'rekey'   - if set to true, returned array will have the
     *                             first column as its first dimension
     *                 'group'   - if set to true and $rekey is set to true, then
     *                             all values with the same first column will be
     *                             wrapped in an array
     *                 'limit'   - number of rows to select
     *                 'offset'  - first row to select
     *                 'select'  - determines what query method to use:
     *                             'one' -> queryOne, 'row' -> queryRow,
     *                             'col' -> queryCol, 'all' ->queryAll (default)
     *                 'selectable_tables' - array list of tables that may be
     *                             joined to in this query, the first element is
     *                             the root table from which the joins are done
     *                 'subgroups' - filter array if all subgroups should be
                                   fetched into a flat array
     * @return bool|array false on failure or array with selected data
     *
     * @access private
     */
    function _getGroupsWithSubgroups($params)
    {
        if ($params['select'] == 'one' || $params['select'] == 'row') {
            // Don't allow 'subgroups' with 'select' if 'one' or 'row'
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Setting "subgroups" requires select to be set to "col" or "all"')
            );
            return false;
        }

        $subgroups = is_array($params['subgroups']) ? $params['subgroups'] : array();

        // Prepare the params for the getGroups call on Perm::Medium.
        $tmp_params = array(
            'fields' => array('group_id'),
            'select' => 'col',
            'filters' => $subgroups,
        );

        $result = parent::getGroups($tmp_params);
        if (!$result) {
            return $result;
        }

        $subgroup_ids = $result;

        // Retrieve all the group_ids from all the subgroups in a do-while loop.
        do {
            // Prepare the params for the getGroups call on Perm::Medium.
            $tmp_params = array(
                'fields' => array(
                    'subgroup_id',
                ),
                'filters' => $subgroups,
                'select' => 'col',
            );

            // Do not include groups that have already been fetched
            $tmp_params['filters']['subgroup_id'] = array(
                'op' => 'NOT IN',
                'value' => $result,
            );

            // Merge 'group_id' filter if needed
            if (array_key_exists('group_id', $tmp_params['filters'])
                && (!is_array($params['filters']['group_id'])
                    || !array_key_exists('value', $params['filters']['group_id'])
                )
            ) {
                $tmp_params['filters']['group_id'] = array_intersect(
                    $subgroup_ids,
                    (array)$params['subgroups']['group_id']
                );
            } else {
                $tmp_params['filters']['group_id'] = $subgroup_ids;
            }

            $subgroup_ids = $this->getGroups($tmp_params);
            if ($subgroup_ids === false) {
                return false;
            }

            $result = array_merge($result, (array)$subgroup_ids);
        // If there were more subgroups, loop again and try to retrieve the subgroups under the current subgroup.
        } while(!empty($subgroup_ids));

        // Merge 'group_id' filter if needed
        if (array_key_exists('filters', $params)
            && array_key_exists('group_id', $params['filters'])
            && (!is_array($params['filters']['group_id'])
                || !array_key_exists('value', $params['filters']['group_id'])
            )
        ) {
            $params['filters']['group_id'] = array_intersect($result, (array)$params['filters']['group_id']);
        } else {
            $params['filters']['group_id'] = $result;
        }
        return parent::getGroups($params);
    }

    /**
     * Fetches groups with their subgroups into a hierarchal structure
     *
     * @param array containing key-value pairs for:
     *                 'fields'  - ordered array containing the fields to fetch
     *                             if empty all fields from the user table are fetched
     *                 'filters' - key values pairs (value may be a string or an array)
     *                 'orders'  - key value pairs (values 'ASC' or 'DESC')
     *                 'rekey'   - if set to true, returned array will have the
     *                             first column as its first dimension
     *                 'group'   - if set to true and $rekey is set to true, then
     *                             all values with the same first column will be
     *                             wrapped in an array
     *                 'limit'   - number of rows to select
     *                 'offset'  - first row to select
     *                 'select'  - determines what query method to use:
     *                             'one' -> queryOne, 'row' -> queryRow,
     *                             'col' -> queryCol, 'all' ->queryAll (default)
     *                 'selectable_tables' - array list of tables that may be
     *                             joined to in this query, the first element is
     *                             the root table from which the joins are done
     *                 'hierarchy' - filter array if all subgroups should be
                                   fetched into a nested array
     * @return bool|array false on failure or array with selected data
     *
     * @access private
     */
    function _getGroupsWithHierarchy($params)
    {
        // Sanity checks on the provided params.
        if (!$params['rekey'] || $params['group'] || $params['select'] != 'all'
            || (reset($params['fields']) !== 'group_id' && reset($params['fields']) !== '*')
        ) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => "Setting 'hierarchy' is only allowed if 'rekey' is enabled, ".
                    "'group' is disabled, 'select' is 'all' and the first field is 'group_id'")
            );
            return false;
        }

        // Get the initial groups.
        $groups = parent::getGroups($params);
        if (!$groups) {
            return $groups;
        }

        // Get the the subgroup id's for these initials groups.
        $tmp_params = array(
            'fields' => array(
                'group_id',
                'subgroup_id',
            ),
            'filters' => array('group_id' => array_keys($groups)),
            'rekey' => true,
            'group' => true,
        );

        $subgroups = $this->getGroups($tmp_params);
        if ($subgroups === false) {
            return false;
        }

        $hierarchy = is_array($params['hierarchy']) ? $params['hierarchy'] : array();

        // Loop through the subgroups. In each loop, perform a recursive 
        // call the the subgroup_ids that are assigned to the current group.
        foreach ($subgroups as $group_id => $subgroup_ids) {
            $params['filters'] = $hierarchy;
            // Merge 'group_id' filter with 'hierachy' if needed
            if (array_key_exists('group_id', $params['filters'])
                && (!is_array($params['filters']['group_id'])
                    || !array_key_exists('value', $params['filters']['group_id'])
                )
            ) {
                $params['filters']['group_id'] = array_intersect(
                    $subgroup_ids,
                    (array)$params['filters']['group_id']
                );
            } else {
                $params['filters']['group_id'] = $subgroup_ids;
            }
            $subgroup_data = $this->_getGroupsWithHierarchy($params);
            if ($subgroup_data === false) {
                return false;
            }
            $groups[$group_id]['subgroups'] = $subgroup_data;
        }

        return $groups;
    }

    /**
     * Fetches rights
     *
     * @param array containing key-value pairs for:
     *                 'fields'  - ordered array containing the fields to fetch
     *                             if empty all fields from the user table are fetched
     *                 'filters' - key values pairs (value may be a string or an array)
     *                 'orders'  - key value pairs (values 'ASC' or 'DESC')
     *                 'rekey'   - if set to true, returned array will have the
     *                             first column as its first dimension
     *                 'group'   - if set to true and $rekey is set to true, then
     *                             all values with the same first column will be
     *                             wrapped in an array
     *                 'limit'   - number of rows to select
     *                 'offset'  - first row to select
     *                 'select'  - determines what query method to use:
     *                             'one' -> queryOne, 'row' -> queryRow,
     *                             'col' -> queryCol, 'all' ->queryAll (default)
     *                 'selectable_tables' - array list of tables that may be
     *                             joined to in this query, the first element is
     *                             the root table from which the joins are done
     *                 'by_group' - if joins should be done using the 'userrights'
     *                             (false default) or through the 'grouprights'
     *                             and 'groupusers' tables (true)
     *                 'inherited' - filter array to fetch all rughts from
                                    (sub)group membership
     *                 'implied'  - filter array for fetching implied rights
     *                 'hierarchy' - filter array for fetching implied rights
                                   into a nested array (overwrites 'implied')
     * @return bool|array false on failure or array with selected data
     *
     * @access public
     */
    function getRights($params = array())
    {
        // Determine of 'inherited', 'implied' or 'hierarchy' is set
        // 'hierarchy' means that 'implied' also is set
        $inherited = array_key_exists('inherited', $params);
        if (array_key_exists('hierarchy', $params)) {
            $hierarchy = $implied = true;
            $params['implied'] = $params['hierarchy'];
        } else {
            $implied = array_key_exists('implied', $params);
            $hierarchy = false;
        }

        // Sanity check on the provided params if the inherited of implied param is set.
        if ($inherited || $implied) {
            $params = LiveUser_Admin_Storage::setSelectDefaultParams($params);

            if (!$params['rekey'] || $params['group'] || $params['select'] !== 'all'
                || (reset($params['fields']) !== 'right_id' && reset($params['fields']) !== '*')
            ) {
                $this->stack->push(
                    LIVEUSER_ADMIN_ERROR, 'exception',
                    array('msg' => "Setting 'inherited', 'implied' or 'hierarchy'".
                        " is only allowed if 'rekey' is enabled, 'group' is disabled".
                        ", 'select' is 'all' and the first field is 'right_id'")
                );
                return false;
            }

            // Extra sanity check on the provided params if the implied param is set
            // (has_implied should be included in the fields.
            if ($implied && !in_array('has_implied', $params['fields'])) {
                $this->stack->push(
                    LIVEUSER_ADMIN_ERROR, 'exception',
                    array('msg' => "Setting 'implied' or 'hierarchy' requires that 'has_implied' field needs to be in the select list")
                );
                return false;
            }
        }

        // handle select, fields and rekey
        $rights = parent::getRights($params);
        if ($rights === false) {
            return false;
        }

        // read rights inherited by (sub)groups
        if ($inherited) {
            // todo: consider adding a NOT IN filter
            $inherited_rights = $this->_getInheritedRights($params);
            if ($inherited_rights === false) {
                return false;
            }

            if (!empty($inherited_rights)) {
                // Loop through the resulting inherited rights, check if they already exist in the current rights array
                // If not: set the type to inherited and add it to the rights array.
                foreach ($inherited_rights as $right_id => $right) {
                    if (isset($rights[$right_id])) {
                        continue;
                    }

                    $right['_type'] = 'inherited';
                    $rights[$right_id] = $right;
                }
            }
        }

        // if the result was empty or no additional work is needed
        if (empty($rights) || !$implied) {
            return $rights;
        }

        if ($implied) {
            $_rights = $rights;
            $rights = array();

            // Loop through the current rights array (backupped in _rights) and
            // set the type to granted if the type isn't set yet.
            foreach ($_rights as $right_id => $right) {
                if (!array_key_exists('_type', $right)) {
                    $right['_type'] = 'granted';
                }
                $rights[$right_id] = $right;
                // If has_implied isn't true, continue (no work to do).
                if (!$right['has_implied']) {
                    continue;
                }

                // todo: consider adding a NOT IN filter
                $implied_rights = $this->_getImpliedRights($params, $right_id);
                if ($implied_rights === false) {
                    return false;
                } elseif (empty($implied_rights)) {
                    continue;
                }

                // Loop through the resulting rights, set the type and order the array based 
                // on the params['implied'] value.
                foreach ($implied_rights as $implied_right_id => $right) {
                    if (isset($rights[$implied_right_id])) {
                        continue;
                    }

                    $right['_type'] = 'implied';

                    // If hierarchy: add the resulting rights to the right they belong to (in implied_rights)
                    if ($hierarchy) {
                        $rights[$right_id]['implied_rights'][$implied_right_id] = $right;
                    } else {
                        $rights[$implied_right_id] = $right;
                    }
                }
            }

            return $rights;
        }

        $params = LiveUser_Admin_Storage::setSelectDefaultParams($params);

        // If the select is set to all (or not set at all) and more than one field is set, 
        // set the type to granted if it isn't set.
        if ($params['select'] == 'all'
           && (count($params['fields']) > 1 || reset($params['fields']) === '*')
        ) {
            foreach ($rights as $right_id => $right) {
                if (!isset($rights[$right_id]['_type']) || !$rights[$right_id]['_type']) {
                    $rights[$right_id]['_type'] = 'granted';
                }
            }
        }

        return $rights;
    }

    /**
     * Fetches implied rights for a given right
     *
     * @param array containing key-value pairs for:
     *                 'fields'  - ordered array containing the fields to fetch
     *                             if empty all fields from the user table are fetched
     *                 'filters' - key values pairs (value may be a string or an array)
     *                 'orders'  - key value pairs (values 'ASC' or 'DESC')
     *                 'rekey'   - if set to true, returned array will have the
     *                             first column as its first dimension
     *                 'group'   - if set to true and $rekey is set to true, then
     *                             all values with the same first column will be
     *                             wrapped in an array
     *                 'limit'   - number of rows to select
     *                 'offset'  - first row to select
     *                 'select'  - determines what query method to use:
     *                             'one' -> queryOne, 'row' -> queryRow,
     *                             'col' -> queryCol, 'all' ->queryAll (default)
     *                 'selectable_tables' - array list of tables that may be
     *                             joined to in this query, the first element is
     *                             the root table from which the joins are done
     *                 'by_group' - if joins should be done using the 'userrights'
     *                             (false default) or through the 'grouprights'
     *                             and 'groupusers' tables (true)
     *                 'implied'  - filter array for fetching implied rights
     * @return bool|array false on failure or array with selected data
     *
     * @access private
     */
    function _getImpliedRights($params, $right_id)
    {
        // Define the selectable tables and define the root table.
        $selectable_tables = array('right_implied', 'rights');
        $root_table = 'right_implied';

        $param = array(
            'fields' => array('implied_right_id'),
            'select' => 'col',
            'filters' => array('right_id' => $right_id),
        );

        // Get the implied_right right_id's.
        // If there are no implied rights (or an error occured), return.
        $result = $this->_makeGet($param, $root_table, $selectable_tables);
        if (!$result) {
            return $result;
        }

        // Merge 'right_id' filter with 'implied' if needed
        $params['filters'] = is_array($params['implied']) ? $params['implied'] : array();
        if (array_key_exists('right_id', $params['filters'])
            && (!is_array($params['filters']['right_id'])
                || !array_key_exists('value', $params['filters']['right_id'])
            )
        ) {
            $params['filters']['right_id'] = array_intersect($result, (array)$params['filters']['right_id']);
        } else {
            $params['filters']['right_id'] = $result;
        }
        return $this->getRights($params);
    }

    /**
     * Fetches all rights gained through subgroup memberships
     *
     * @param array containing key-value pairs for:
     *                 'fields'  - ordered array containing the fields to fetch
     *                             if empty all fields from the user table are fetched
     *                 'filters' - key values pairs (value may be a string or an array)
     *                 'orders'  - key value pairs (values 'ASC' or 'DESC')
     *                 'rekey'   - if set to true, returned array will have the
     *                             first column as its first dimension
     *                 'group'   - if set to true and $rekey is set to true, then
     *                             all values with the same first column will be
     *                             wrapped in an array
     *                 'limit'   - number of rows to select
     *                 'offset'  - first row to select
     *                 'select'  - determines what query method to use:
     *                             'one' -> queryOne, 'row' -> queryRow,
     *                             'col' -> queryCol, 'all' ->queryAll (default)
     *                 'selectable_tables' - array list of tables that may be
     *                             joined to in this query, the first element is
     *                             the root table from which the joins are done
     *                 'by_group' - if joins should be done using the 'userrights'
     *                             (false default) or through the 'grouprights'
     *                             and 'groupusers' tables (true)
     *                 'inherited' - filter array to fetch all rughts from
                                    (sub)group membership
     * @return bool|array false on failure or array with selected data
     *
     * @access private
     */
    function _getInheritedRights($params)
    {
        // Prepare the params with the provided fiters in the params array.
        $param = array(
            'fields' => array('group_id'),
            'select' => 'col',
            'filters' => is_array($params['inherited']) ? $params['inherited'] : array(),
            'subgroups' => is_array($params['inherited']) ? $params['inherited'] : array(),
        );

        // Get the groups based on the params.
        // If there are no subgroups (or an error occured), return.
        $result = $this->getGroups($param);
        if (!$result) {
            return $result;
        }

        // Merge 'group_id' filter if needed
        if (array_key_exists('filters', $params)
            && array_key_exists('group_id', $params['filters'])
            && (!is_array($params['filters']['group_id'])
                || !array_key_exists('value', $params['filters']['group_id'])
            )
        ) {
            $params['filters']['group_id'] = array_intersect($result, (array)$params['filters']['group_id']);
        } else {
            $params['filters']['group_id'] = $result;
        }
        $params['by_group'] = true;
        unset($params['inherited']);
        return $this->getRights($params);
    }
}
?>
