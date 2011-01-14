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
 * @version CVS: $Id: MDB2.php,v 1.33 2006/05/25 08:20:34 lsmith Exp $
 * @link http://pear.php.net/LiveUser_Admin
 */

/**
 * Require parent class definition.
 */
require_once 'LiveUser/Admin/Storage/SQL.php';
require_once 'MDB2.php';

/**
 * This is a PEAR::MDB2 backend driver for the LiveUser class.
 * A PEAR::MDB2 connection object can be passed to the constructor to reuse an
 * existing connection. Alternatively, a DSN can be passed to open a new one.
 *
 * Requirements:
 * - File "Liveuser/Admin.php" (contains the parent class "LiveUser_Admin")
 * - Array of connection options or a PEAR::MDB2 connection object must be
 *   passed to the init() method
 *   Example: array('dsn' => 'mysql://user:pass@host/db_name')
 *              OR
 *            array('dbc' => &$conn) ($conn is a PEAR::MDB2 connection object)
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
class LiveUser_Admin_Storage_MDB2 extends LiveUser_Admin_Storage_SQL
{
    /**
     * Database connection functions
     *
     * @var    object
     * @access private
     */
    var $function = 'connect';

    /**
     * determines of the use of sequences should be forced
     *
     * @var bool
     * @access private
     */
    var $force_seq = true;

    /**
     * Initializes database storage container.
     * Connects to database or uses existing database connection.
     *
     * @param array Storage Configuration
     * @param array containing the database structure (tables, fields, alias)
     * @return bool true on success and false on failure
     *
     * @access public
     */
    function init(&$storageConf, $structure)
    {
        parent::init($storageConf, $structure);

        if (!MDB2::isConnection($this->dbc) && !is_null($this->dsn)) {
            $this->options['portability'] = MDB2_PORTABILITY_ALL;
            if ($this->function == 'singleton') {
                $dbc =& MDB2::singleton($storageConf['dsn'], $this->options);
            } else {
                $dbc =& MDB2::connect($storageConf['dsn'], $this->options);
            }
            if (PEAR::isError($dbc)) {
                $this->stack->push(LIVEUSER_ERROR_INIT_ERROR, 'error',
                    array('container' => 'could not connect: '.$dbc->getMessage(),
                    'debug' => $dbc->getUserInfo()));
                return false;
            }
            $this->dbc =& $dbc;
        }

        if (!MDB2::isConnection($this->dbc)) {
            $this->stack->push(LIVEUSER_ERROR_INIT_ERROR, 'error',
                array('container' => 'storage layer configuration missing'));
            return false;
        }

        return true;
    }

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string text string value that is intended to be converted.
     * @param string type to which the value should be converted to
     * @return stringtext string that represents the given argument value in
     *       a DBMS specific format.
     *
     * @access public
     * @uses MDB2::quote
     */
    function quote($value, $type)
    {
        return $this->dbc->quote($value, $type);
    }

    /**
     * Apply a type to all values of an array and return as a comma
     * seperated string useful for generating IN statements
     *
     * @param array data array
     * @param string determines type of the field
     * @return string comma seperated values
     *
     * @access public
     * @uses MDB2_Driver_Datatype_Common::implodeArray
     */
    function implodeArray($array, $type)
    {
        $this->dbc->loadModule('Datatype');
        return $this->dbc->datatype->implodeArray($array, $type);
    }

    /**
     *  Sets the range of the next query
     *
     * @param string number of rows to select
     * @param string first row to select
     *
     * @return bool true on success and false on failure
     *
     * @access public
     * @uses MDB2::setLimit
     */
    function setLimit($limit, $offset)
    {
        if ($limit || $offset) {
            $result = $this->dbc->setLimit($limit, $offset);
            if (PEAR::isError($result)) {
                $this->stack->push(
                    LIVEUSER_ADMIN_ERROR_QUERY_BUILDER, 'exception',
                    array('reason' => $result->getMessage() . '-' . $result->getUserInfo())
                );
                return false;
            }
        }
        return true;
    }

    /**
     * Execute DML query
     *
     * @param string DML query
     * @return bool|int of the affected rows
     *
     * @access public
     * @uses MDB2::exec
     */
    function exec($query)
    {
        $result = $this->dbc->exec($query);
        if (PEAR::isError($result)) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR_QUERY_BUILDER, 'exception',
                array('reason' => $result->getMessage() . '-' . $result->getUserInfo())
            );
            return false;
        }
        return $result;
    }

    /**
     * Execute the specified query, fetch the value from the first column of
     * the first row of the result set and then frees the result set.
     *
     * @param string the SELECT query statement to be executed.
     * @param string argument that specifies the expected datatype of the
     *       result set field, so that an eventual conversion may be performed.
     *       The default datatype is text, meaning no conversion is performed.
     * @return bool|string
     *
     * @access public
     * @uses MDB2::queryOne
     */
    function queryOne($query, $type)
    {
        $result = $this->dbc->queryOne($query, $type);
        if (PEAR::isError($result)) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR_QUERY_BUILDER, 'exception',
                array('reason' => $result->getMessage() . '-' . $result->getUserInfo())
            );
            return false;
        }
        return $result;
    }

    /**
     * Execute the specified query, fetch the values from the first
     * row of the result set into an array and then frees
     * the result set.
     *
     * @param string the SELECT query statement to be executed.
     * @param array argument that specifies a list of expected datatypes
     *       of theresult set columns, so that the conversions may be performed.
     *       The default datatype is text, meaning no conversion is performed.
     * @return bool|array
     *
     * @access public
     * @uses MDB2::queryRow
     */
    function queryRow($query, $type)
    {
        $result = $this->dbc->queryRow($query, $type, MDB2_FETCHMODE_ASSOC);
        if (PEAR::isError($result)) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR_QUERY_BUILDER, 'exception',
                array('reason' => $result->getMessage() . '-' . $result->getUserInfo())
            );
            return false;
        }
        return $result;
    }

    /**
     * Execute the specified query, fetch the value from the first column of
     * each row of the result set into an array and then frees the result set.
     *
     * @param string the SELECT query statement to be executed.
     * @param string argument that specifies the expected datatype of the
     *       result set field, so that an eventual conversion may be performed.
     *       The default datatype is text, meaning no conversion is performed.
     * @return bool|array
     *
     * @access public
     * @uses MDB2::queryCol
     */
    function queryCol($query, $type)
    {
        $result = $this->dbc->queryCol($query, $type);
        if (PEAR::isError($result)) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR_QUERY_BUILDER, 'exception',
                array('reason' => $result->getMessage() . '-' . $result->getUserInfo())
            );
            return false;
        }
        return $result;
    }

    /**
     * Execute the specified query, fetch all the rows of the result set into
     * a two dimensional array and then frees the result set.
     *
     * @param string the SELECT query statement to be executed.
     * @param array argument that specifies a list of expected datatypes
     *       of theresult set columns, so that the conversions may be performed.
     *       The default datatype is text, meaning no conversion is performed.
     * @param bool if set to true, returned array will have the first
     *       column as its first dimension
     * @param bool if set to true and $rekey is set to true, then
     *      all values with the same first column will be wrapped in an array
     * @return bool|array
     *
     * @access public
     * @uses MDB2::queryAll
     */
    function queryAll($query, $types, $rekey, $group)
    {
        $result = $this->dbc->queryAll($query, $types, MDB2_FETCHMODE_ASSOC, $rekey, false, $group);
        if (PEAR::isError($result)) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR_QUERY_BUILDER, 'exception',
                array('reason' => $result->getMessage() . '-' . $result->getUserInfo())
            );
            return false;
        }
        return $result;
    }

    /**
     * returns the next free id of a sequence
     *
     * @param string name of the sequence
     * @param bool when true the seqence is
     *                           automatic created, if it not exists
     * @return bool|int false on failure or next id for the table
     *
     * @access public
     * @uses MDB2::nextId
     */
    function nextId($seqname, $ondemand = true)
    {
        $result = $this->dbc->nextId($seqname, $ondemand);
        if (PEAR::isError($result)) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR_QUERY_BUILDER, 'exception',
                array('reason' => $result->getMessage() . '-' . $result->getUserInfo())
            );
            return false;
        }
        return $result;
    }

    /**
     * returns the next free id of a sequence if the RDBMS
     * does not support auto increment
     *
     * @param string name of the table into which a new row was inserted
     * @param string name of the field into which a new row was inserted
     * @param bool when true the seqence is automatic created, if it not exists
     * @return bool|int
     *
     * @access public
     * @uses MDB2::nextId MDB2_Extended::getBeforeId
     */
    function getBeforeId($table, $field, $ondemand = true)
    {
        if ($this->force_seq) {
            $result = $this->dbc->nextId($table, $ondemand);
        } else {
            $this->dbc->loadModule('Extended');
            $result = $this->dbc->extended->getBeforeId($table, $field, $ondemand, false);
        }

        if (PEAR::isError($result)) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR_QUERY_BUILDER, 'exception',
                array('reason' => $result->getMessage() . '-' . $result->getUserInfo())
            );
            return false;
        }
        return $result;
    }

    /**
     * returns the autoincrement ID if supported or $id
     *
     * @param string value as returned by getBeforeId()
     * @param string name of the table into which a new row was inserted
     * @param string name of the field into which a new row was inserted
     * @return  bool|int returns the id that the users passed via params
     *
     * @access public
     * @uses MDB2_Extended::getAfterId
     */
    function getAfterId($id, $table, $field)
    {
        if ($this->force_seq) {
            return $id;
        }

        $this->dbc->loadModule('Extended');
        $result = $this->dbc->extended->getAfterId($id, $table, $field);
        if (PEAR::isError($result)) {
            $this->stack->push(
                LIVEUSER_ADMIN_ERROR_QUERY_BUILDER, 'exception',
                array('reason' => $result->getMessage() . '-' . $result->getUserInfo())
            );
            return false;
        }
        return $result;
    }
}
?>
