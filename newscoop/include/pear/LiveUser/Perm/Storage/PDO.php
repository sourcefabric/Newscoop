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
 * @version CVS: $Id: PDO.php,v 1.13 2006/06/05 09:54:58 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */

/**
 * Require parent class definition.
 */
require_once 'LiveUser/Perm/Storage/SQL.php';

/**
 * PDO container for permission handling.
 *
 * This is a PDO backend driver for the LiveUser class.
 * A PDO connection object can be passed to the constructor to reuse an
 * existing connection. Alternatively, a DSN can be passed to open a new one.
 *
 * Requirements:
 * - PHP5
 * - File "Liveuser.php" (contains the parent class "LiveUser")
 * - Array of connection options
 *   passed to the constructor.
 *   Example: array('dsn'     => 'mysql:host:localhost;dbname=db_name',
 *                  'options' => array('username' => 'root', 'password' => 'secret', 'attr' => array()));
 *
 * @category  authentication
 * @package   LiveUser
 * @author    Arnaud Limbourg <arnaud@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license   http://www.gnu.org/licenses/lgpl.txt
 * @version   Release: @package_version@
 * @link      http://pear.php.net/LiveUser
 */
class LiveUser_Perm_Storage_PDO extends LiveUser_Perm_Storage_SQL
{
    /**
     * determines of the use of sequences should be forced
     *
     * @var bool
     * @access private
     */
    var $force_seq = true;

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
        parent::init($storageConf);

        if (!is_a($this->dbc, 'pdo') && !is_null($this->dsn)) {
            $login = $password = $extra = null;
            if (!empty($this->options)) {
                if (array_key_exists('username', $this->options)) {
                    $login = $this->options['username'];
                }
                if (array_key_exists('password', $this->options)) {
                    $password = $this->options['password'];
                }
                if (array_key_exists('attr', $this->options)) {
                    $extra = $this->options['attr'];
                }
            }
            try {
                $dbc = new PDO($this->dsn, $login, $password, $extra);
            } catch (PDOException $e) {
                $this->stack->push(LIVEUSER_ERROR_INIT_ERROR, 'error',
                    array(
                        'container' => 'could not connect: ' . $e->getMessage(),
                        'debug'     => $e->getTrace()
                    )
                );
                return false;
            }
            $this->dbc = $dbc;
        }

        if (!is_a($this->dbc, 'pdo')) {
            $this->stack->push(LIVEUSER_ERROR_INIT_ERROR, 'error',
                array('container' => 'storage layer configuration missing'));
            return false;
        }

        return true;
    }

    /**
     * map an auth user to a perm user
     *
     * @param int           auth user id
     * @param string        name of the container
     * @return array|false  requested data or false on failure
     *
     * @access public
     */
    function mapUser($auth_user_id, $containerName)
    {
        $query = '
            SELECT
                ' . $this->alias['perm_user_id'] . ' AS perm_user_id,
                ' . $this->alias['perm_type'] . '    AS perm_type
            FROM
                '.$this->prefix.$this->alias['perm_users'].'
            WHERE
                ' . $this->alias['auth_user_id'] . ' = '.
                    $this->dbc->quote($auth_user_id).'
            AND
                ' . $this->alias['auth_container_name'] . ' = '.
                    $this->dbc->quote((string)$containerName);

        $result = $this->dbc->query($query);

        if ($result === false) {
            $error_info = $this->dbc->errorInfo();
            $this->stack->push(LIVEUSER_ERROR, 'exception', array(),
                'error in query ' . $error_info[2] . ' - ' . $query);
            return false;
        }

        $row = $result->fetch(PDO::FETCH_ASSOC);

        if ($row === false && $this->dbc->errorCode() === '00000') {
            $row = null;
        }

        return $row;
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
        $query = '
            SELECT
                ' . $this->alias['right_id'] . ',
                ' . $this->alias['right_level'] . '
            FROM
                '.$this->prefix.$this->alias['userrights'].'
            WHERE
                ' . $this->alias['perm_user_id'] . ' = '.
                    $this->dbc->quote($perm_user_id);

        $result = $this->dbc->query($query);

        if ($result === false) {
            $error_info = $this->dbc->errorInfo();
            $this->stack->push(LIVEUSER_ERROR, 'exception', array(),
                'error in query ' . $error_info[2] . ' - ' . $query);
            return false;
        }

        $rows = array();
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $rows[$row[0]] = $row[1];
        }

        return $rows;
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
        // get all areas in which the user is area admin
        $query = '
            SELECT
                R.' . $this->alias['right_id'] . ' AS right_id,
                '.LIVEUSER_MAX_LEVEL.'             AS right_level
            FROM
                '.$this->prefix.$this->alias['area_admin_areas'].' AAA,
                '.$this->prefix.$this->alias['rights'].' R
            WHERE
                AAA.area_id = R.area_id
            AND
                AAA.' . $this->alias['perm_user_id'] . ' = '.
                    $this->dbc->quote($perm_user_id);

        $result = $this->dbc->query($query);

        if ($result === false) {
            $error_info = $this->dbc->errorInfo();
            $this->stack->push(LIVEUSER_ERROR, 'exception', array(),
                'error in query ' . $error_info[2] . ' - ' . $query);
            return false;
        }

        $rows = array();
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $rows[$row[0]] = $row[1];
        }

        return $rows;
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
        $query = '
            SELECT
                GU.' . $this->alias['group_id'] . '
            FROM
                '.$this->prefix.$this->alias['groupusers'].' GU,
                '.$this->prefix.$this->alias['groups'].' G
            WHERE
                GU.' . $this->alias['group_id'] . ' = G. ' . $this->alias['group_id'] . '
            AND
                GU.' . $this->alias['perm_user_id'] . ' = '.
                    $this->dbc->quote($perm_user_id);

        if (array_key_exists('is_active', $this->tables['groups']['fields'])) {
            $query .= ' AND
                G.' . $this->alias['is_active'] . '=' .
                    $this->dbc->quote(true);
        }

        $result = $this->dbc->query($query);

        if ($result === false) {
            $error_info = $this->dbc->errorInfo();
            $this->stack->push(LIVEUSER_ERROR, 'exception', array(),
                'error in query ' . $error_info[2] . ' - ' . $query);
            return false;
        }

        $col = $result->fetchAll(PDO::FETCH_COLUMN);

        return $col;
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
        $query = '
            SELECT
                GR.' . $this->alias['right_id'] . ',
                MAX(GR.' . $this->alias['right_level'] . ')
            FROM
                '.$this->prefix.$this->alias['grouprights'].' GR
            WHERE
                GR.' . $this->alias['group_id'] . ' IN('.
                    implode(', ', $group_ids).')
            GROUP BY
                GR.' . $this->alias['right_id'] . '';

        $result = $this->dbc->query($query);

        if ($result === false) {
            $error_info = $this->dbc->errorInfo();
            $this->stack->push(LIVEUSER_ERROR, 'exception', array(),
                'error in query ' . $error_info[2] . ' - ' . $query);
            return false;
        }

        $rows = array();
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $rows[$row[0]] = $row[1];
        }

        return $rows;
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
        $query = '
            SELECT
                DISTINCT SG.' . $this->alias['subgroup_id'] . '
            FROM
                '.$this->prefix.$this->alias['groups'].' G,
                '.$this->prefix.$this->alias['group_subgroups'].' SG
            WHERE
                SG.' . $this->alias['subgroup_id'] . ' = G.' .
                    $this->alias['group_id'] . '
            AND
                SG.' . $this->alias['group_id'] . ' IN ('.
                    implode(', ', $newGroupIds).')
            AND
                SG.' . $this->alias['subgroup_id'] . ' NOT IN ('.
                    implode(', ', $group_ids).')';

        if (array_key_exists('is_active', $this->tables['groups']['fields'])) {
            $query .= ' AND
                G.' . $this->alias['is_active'] . '=' .
                    $this->dbc->quote(true);
        }

        $result = $this->dbc->query($query);

        if ($result === false) {
            $error_info = $this->dbc->errorInfo();
            $this->stack->push(LIVEUSER_ERROR, 'exception', array(),
                'error in query ' . $error_info[2] . ' - ' . $query);
            return false;
        }

        $col = $result->fetchAll(PDO::FETCH_COLUMN);

        return $col;
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
        $query = '
            SELECT
            DISTINCT
                TR.' . $this->alias['right_level'] . ',
                TR.' . $this->alias['right_id'] . '
            FROM
                '.$this->prefix.$this->alias['rights'].' R,
                '.$this->prefix.$this->alias[$table.'rights'].' TR
            WHERE
                TR.' . $this->alias['right_id'] . ' = R.' . $this->alias['right_id'] . '
            AND
                R.' . $this->alias['right_id'] . ' IN ('.
                    implode(', ', array_keys($rightIds)).')
            AND
                R.' . $this->alias['has_implied'] . '='.
                    $this->dbc->quote(true);

        $result = $this->dbc->query($query);

        if ($result === false) {
            $error_info = $this->dbc->errorInfo();
            $this->stack->push(LIVEUSER_ERROR, 'exception', array(),
                'error in query ' . $error_info[2] . ' - ' . $query);
            return false;
        }

        $rows = array();
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $rows[$row[0]][] = $row[1];
        }

        return $rows;
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
        $query = '
            SELECT
                RI.' . $this->alias['implied_right_id'] . ' AS right_id,
                '.$currentLevel.'                           AS right_level,
                R.' . $this->alias['has_implied'] . '       AS has_implied
            FROM
                '.$this->prefix.$this->alias['rights'].' R,
                '.$this->prefix.$this->alias['right_implied'].' RI
            WHERE
                RI.' . $this->alias['implied_right_id'] . ' = R.' . $this->alias['right_id'] . '
            AND
                RI.' . $this->alias['right_id'] . ' IN ('.
                    implode(', ', $currentRights).')';

        $result = $this->dbc->query($query);

        if ($result === false) {
            $error_info = $this->dbc->errorInfo();
            $this->stack->push(LIVEUSER_ERROR, 'exception', array(),
                'error in query ' . $error_info[2] . ' - ' . $query);
            return false;
        }

        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        return (array)$rows;
    }

    /**
     * Override the disconnect method from the parent class.
     *
     * @return void
     */
    function disconnect()
    {
        if ($this->dsn) {
            $this->dbc = null;
        }
    }
}
?>
