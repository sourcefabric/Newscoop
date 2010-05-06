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
 * @version CVS: $Id: Common.php,v 1.37 2006/08/15 10:38:55 lsmith Exp $
 * @link http://pear.php.net/LiveUser_Admin
 */

/**
 * Base class for authentication backends.
 *
 * @category authentication
 * @package LiveUser_Admin
 * @author   Lukas Smith <smith@pooteeweet.org>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser_Admin
 */
class LiveUser_Admin_Auth_Common
{
    /**
     * Error stack
     *
     * @var object PEAR_ErrorStack
     * @access public
     */
    var $stack = null;

    /**
     * Storage Container
     *
     * @var    LiveUser_Admin_Storage
     * @access private
     */
    var $_storage = null;

    /**
     * Key (method names), with array lists of selectable tables for the given method
     *
     * @var array
     * @access public
     */
    var $selectable_tables = array(
        'getUsers' => array('users'),
    );

    /**
     * Set posible encryption modes.
     *
     * @access private
     * @var    array
     */
    var $encryptionModes = array(
        'MD5'   => 'MD5',
        'RC4'   => 'RC4',
        'PLAIN' => 'PLAIN',
        'SHA1'  => 'SHA1'
    );

    /**
     * Defines the algorithm used for encrypting/decrypting
     * passwords. Default: "MD5".
     *
     * @access private
     * @var    string
     */
    var $passwordEncryptionMode = 'MD5';

    /**
     * Defines the secret to use for encryption if needed
     *
     * @access protected
     * @var    string
     */
    var $secret;

    /**
     * The name associated with this auth container. The name is used
     * when adding users from this container to the reference table
     * in the permission container. This way it is possible to see
     * from which auth container the user data is coming from.
     *
     * @var    string
     * @access public
     */
    var $containerName = null;

    /**
     * Class constructor. Feel free to override in backend subclasses.
     *
     * @access protected
     */
    function LiveUser_Admin_Auth_Common()
    {
        $this->stack = &PEAR_ErrorStack::singleton('LiveUser_Admin');
    }

    /**
     * Initialize the storage container
     *
     * @access  public
     * @param   array contains configuration of the container
     * @param   string name of container
     * @return  bool true on success or false on failure
     */
    function init(&$conf, $containerName)
    {
        $this->containerName = $containerName;
        if (!array_key_exists('storage', $conf)) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
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

        $storageConf = array();
        $storageConf[$conf['type']] =& $conf['storage'];
        $this->_storage = LiveUser::storageFactory($storageConf, 'LiveUser_Admin_Auth_');
        if ($this->_storage === false) {
            $this->stack->push(LIVEUSER_ADMIN_ERROR, 'exception',
                array('msg' => 'Could not instanciate auth storage container: '.$conf['type']));
            return false;
        }

        return true;
    }

    /**
     * Decrypts a password so that it can be compared with the user input.
     * Uses the algorithm defined in the passwordEncryptionMode property.
     *
     * @param  string the encrypted password
     * @return string the decrypted password
     *
     * @access public
     */
    function decryptPW($encryptedPW)
    {
        return LiveUser::decryptPW($encryptedPW, $this->passwordEncryptionMode, $this->secret);
    }

    /**
     * Encrypts a password for storage in a backend container.
     * Uses the algorithm defined in the passwordEncryptionMode property.
     *
     * @param string  encryption type
     * @return string the encrypted password
     *
     * @access public
     */
    function encryptPW($plainPW)
    {
        return LiveUser::encryptPW($plainPW, $this->passwordEncryptionMode, $this->secret);
    }

    /**
     * Add a user
     *
     * @param array containing atleast the key-value-pairs of all required
     *              columns in the users table
     * @return int|bool false on error, true (or new id) on success
     *
     * @access public
     */
    function addUser($data)
    {
        // todo: does this work?
        if (array_key_exists('passwd', $data)) {
            $data['passwd'] = $this->encryptPW($data['passwd']);
        }
        $result = $this->_storage->insert('users', $data);
        // todo: notify observer
        return $result;
    }

    /**
     * Update a user
     *
     * @param array containing the key value pairs of columns to update
     * @param array key values pairs (value may be a string or an array)
     *                      This will construct the WHERE clause of your update
     *                      Be careful, if you leave this blank no WHERE clause
     *                      will be used and all users will be affected by the update
     * @return int|bool false on error, the affected rows on success
     *
     * @access public
     */
    function updateUser($data, $filters)
    {
        if (array_key_exists('passwd', $data)) {
            $data['passwd'] = $this->encryptPW($data['passwd']);
        }
        $result = $this->_storage->update('users', $data, $filters);
        // todo: notify observer
        return $result;
    }

    /**
     * Remove a user
     *
     * @param array key values pairs (value may be a string or an array)
     *                      This will construct the WHERE clause of your update
     *                      Be careful, if you leave this blank no WHERE clause
     *                      will be used and all users will be affected by the update
     * @return int|bool false on error, the affected rows on success
     *
     * @access public
     */
    function removeUser($filters)
    {
        $result = $this->_storage->delete('users', $filters);
        // todo: notify observer
        return $result;
    }

    /**
     * Fetches users
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
     * @return bool|array false on failure or array with selected data
     *
     * @access public
     */
    function getUsers($params = array())
    {
        $selectable_tables = array();
        if (array_key_exists('selectable_tables', $params)) {
            $selectable_tables = $params['selectable_tables'];
        } elseif (array_key_exists('getUsers', $this->selectable_tables)) {
            $selectable_tables = $this->selectable_tables['getUsers'];
        }
        $root_table = reset($selectable_tables);

        $params = LiveUser_Admin_Storage::setSelectDefaultParams($params);

        return $this->_storage->select($params['select'], $params['fields'],
            $params['filters'], $params['orders'], $params['rekey'], $params['group'],
            $params['limit'], $params['offset'], $root_table, $selectable_tables);
    }

    /**
     * properly disconnect from resources
     *
     * @access  public
     */
    function disconnect()
    {
        $this->_storage->disconnect();
    }
}
