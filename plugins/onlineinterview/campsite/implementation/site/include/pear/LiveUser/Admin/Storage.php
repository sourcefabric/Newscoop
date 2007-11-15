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
 * @version CVS: $Id: Storage.php,v 1.17 2006/06/09 09:20:36 lsmith Exp $
 * @link http://pear.php.net/LiveUser_Admin
 */

/**
 * This is core base class for all LiveUser admin storage classes that is meant
 * for internal use only.
 *
 * @category authentication
 * @package LiveUser_Admin
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @author  Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser_Admin
 */
class LiveUser_Admin_Storage
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
     * @return void
     */
    function LiveUser_Admin_Storage()
    {
        $this->stack = &PEAR_ErrorStack::singleton('LiveUser_Admin');
    }

    /**
     * Initializes database storage container.
     * Goes through the storage config and turns each value into
     * a var
     *
     * @param array Storage Configuration
     * @param array containing the database structure (tables, fields, alias)
     * @return bool true on success and false on failure
     *
     * @access public
     */
    function init(&$storageConf, $structure)
    {
        if (is_array($storageConf)) {
            $keys = array_keys($storageConf);
            foreach ($keys as $key) {
                if (isset($this->$key)) {
                    $this->$key =& $storageConf[$key];
                }
            }
        }

        if (empty($this->tables)) {
            $this->tables = $structure['tables'];
        } else {
            $this->tables = LiveUser::arrayMergeClobber($structure['tables'], $this->tables);
        }
        if (empty($this->fields)) {
            $this->fields = $structure['fields'];
        } else {
            $this->fields = LiveUser::arrayMergeClobber($structure['fields'], $this->fields);
        }
        if (empty($this->alias)) {
            $this->alias = $structure['alias'];
        } else {
            $this->alias = LiveUser::arrayMergeClobber($structure['alias'], $this->alias);
        }

        return true;
    }

    /**
     * Static method to set defaults into a select params array
     *
     * @param array params array
     * @return array params array
     *
     * @access public
     */
    function setSelectDefaultParams($params)
    {
        if (!is_array($params)) {
            PEAR_ErrorStack::staticPush('LiveUser_Admin', LIVEUSER_ADMIN_ERROR,
                'exception', array(), 'Parameters must be an array but is of type: '.gettype($params));
        }

        $params['fields'] = empty($params['fields']) ? array('*') : $params['fields'];
        $params['with'] = empty($params['with']) ? array() : $params['with'];
        $params['filters'] = empty($params['filters']) ? array() : $params['filters'];
        $params['orders'] = empty($params['orders']) ? array() : $params['orders'];
        $params['rekey'] = empty($params['rekey']) ? false : $params['rekey'];
        $params['group'] = empty($params['group']) ? false : $params['group'];
        $params['limit'] = empty($params['limit']) ? null : $params['limit'];
        $params['offset'] = empty($params['offset']) ? null : $params['offset'];
        $params['select'] = empty($params['select']) ? 'all' : $params['select'];

        return $params;
    }

    /**
     * properly disconnect from resources
     *
     * @access  public
     */
    function disconnect()
    {
    }
}
?>
