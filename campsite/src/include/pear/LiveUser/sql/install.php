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
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version CVS: $Id: install.php,v 1.51 2006/05/01 10:46:30 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */

require_once 'LiveUser.php';
require_once 'MDB2/Schema.php';

/* ATTENTION: uncomment the following lines as needed

// error handler
function handleError($err)
{
   var_dump($err);
   return PEAR_ERRORSTACK_PUSH;
}

PEAR_ErrorStack::setDefaultCallback('handleError');

echo '<pre>';

// customize DSN as needed
$dsn = 'mysql://root:@localhost/liveuser_test_installer';

// customize config array as needed
$conf = array(
    'authContainers' => array(
        array(
            'type'         => 'MDB2',
            'expireTime'   => 3600,
            'idleTime'     => 1800,
            'storage' => array(
                'dsn' => $dsn,
#                'force_seq' => false,
                'alias' => array(
                    'auth_user_id' => 'authUserId',
                    'lastlogin' => 'lastLogin',
                    'is_active' => 'isActive',
                    'owner_user_id' => 'owner_user_id',
                    'owner_group_id' => 'owner_group_id',
                ),
                'fields' => array(
#                    'auth_user_id' => 'integer',
                    'lastlogin' => 'timestamp',
                    'is_active' => 'boolean',
                    'owner_user_id' => 'integer',
                    'owner_group_id' => 'integer',
                ),
                'tables' => array(
                    'users' => array(
                        'fields' => array(
                            'lastlogin' => null,
                            'is_active' => null,
                            'owner_user_id' => null,
                            'owner_group_id' => null,
                        ),
                    ),
                ),
            ),
        ),
    ),
    'permContainer'  => array(
        'type'  => 'Complex',
        'storage' => array(
            'MDB2' => array(
                'dsn' => $dsn,
                'prefix' => 'liveuser_',
#                'force_seq' => false,
                'fields' => array(
#                    'auth_user_id' => 'integer',
                ),
            )
        )
    )
);

@unlink('dump.sql');
function dump_to_file(&$db, $scope, $message, $is_manip)
{
    if ($is_manip) {
        $fp = fopen('dump.sql', 'a');
        fwrite($fp, $message."\n");
        fclose($fp);
    }
}

// customize MDB2_SCHEMA configuration options as needed
$options = array(
    'debug' => true,
    'log_line_break' => '<br>',
// to dump the SQL to a file uncommented the following line
// and set the disable_query parameter in the installSchema calls
#    'debug_handler' => 'dump_to_file',
);

// field name - value pairs of lengths to use in the schema
$lengths = array('description' => 255);

// field name - value pairs of defaults to use in the schema
$defaults = array('right_level' => LIVEUSER_MAX_LEVEL);

// create instance of the auth container
$auth =& LiveUser::authFactory($conf['authContainers'][0], 'foo');
// generate xml schema file for auth container
$result = LiveUser_Misc_Schema_Install::generateSchema(
    $auth,
    'auth_schema.xml',
    $lengths,
    $defaults
);
var_dump($result);

// install the auth xml schema .. notice the 4th parameter controls if the
// database needs to be created or not
$variables = array();
$result = LiveUser_Misc_Schema_Install::installSchema(
    $auth,
    'auth_schema.xml',
    $variables,
    true,
    $options,
    false,
    false
);
var_dump($result);

// create instance of the perm container
$perm =& LiveUser::storageFactory($conf['permContainer']['storage']);
// generate xml schema file for perm container
$result = LiveUser_Misc_Schema_Install::generateSchema(
    $perm,
    'perm_schema.xml',
    $lengths,
    $defaults
);
var_dump($result);

// install the perm xml schema .. notice the 4th parameter controls if the
// database needs to be created or not
$variables = array();
$result = LiveUser_Misc_Schema_Install::installSchema(
    $perm,
    'perm_schema.xml',
    $variables,
    false,
    $options,
    false,
    false
);
var_dump($result);

/* */

/**
 * database schema installer class
 *
 * This class generates XML based schema files and uses PEAR:MDB2_Schema to
 * install them inside the users database
 *
 * Requirements:
 * - PEAR::LiveUser
 * - PEAR::MDB2_Schema
 * - PEAR::MDB2
 * - PEAR::MDB2_Driver_* (where * is the name of the backend database)
 * - a valid LiveUser configuration
 *
 * @category authentication
 * @package LiveUser
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @version $Id: install.php,v 1.51 2006/05/01 10:46:30 lsmith Exp $
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser_Misc_Schema_Install
{

    /**
     * Accepts a PDO DSN and returns a PEAR DSN
     *
     * The PEAR DSN format is specified here:
     * http://pear.php.net/manual/en/package.database.db.intro-dsn.php
     *
     * @param string PDO DSN
     * @return array PEAR DSN
     *
     * @access public
     */
    function parsePDODSN($pdo_dsn)
    {
        die('Hardcode the parsed DSN to the PEAR array dsn format.');
        return array(
            'phptype'  => false,
            'dbsyntax' => false,
    // not needed as its fetched from the options array
    #        'username' => false,
    #        'password' => false,
            'protocol' => false,
            'hostspec' => false,
            'port'     => false,
            'socket'   => false,
            'database' => false,
            'mode'     => false,
        );
    }

    /**
     * Generates a schema file from the instance
     *
     * @param object LiveUser storage instance
     * @param string name of the file into which the xml schema should be written
     * @param array key-value pairs with keys being field names and values being the default length
     * @param array key-value pairs with keys being field names and values being the default values
     * @return bool|PEAR_Error true on success or a PEAR_Error on error
     *
     * @access public
     */
    function generateSchema($obj, $file, $lengths = array(), $defaults = array())
    {
        if (!is_object($obj)) {
            return false;
        }

        $use_auto_increment = false;
        if (isset($obj->force_seq) && !$obj->force_seq) {
            if (MDB2::isConnection($obj->dbc)) {
                $use_auto_increment = ($obj->dbc->supports('auto_increment') === true);
            } elseif (is_a($obj->dbc, 'PDO')) {
                // todo: need to figure out what to do here
                $use_auto_increment = true;
            }
        }

        // generate xml schema
        $tables = array();
        $sequences = array();
        foreach ($obj->tables as $table_name => $table) {
            $fields = array();
            $table_indexes = array();
            foreach($table['fields'] as $field_name => $required) {
                $type = $obj->fields[$field_name];
                $field_name = $obj->alias[$field_name];
                $fields[$field_name]['name'] = $field_name;
                $fields[$field_name]['type'] = $type;
                if ($fields[$field_name]['type'] == 'text') {
                    $length = array_key_exists($field_name, $lengths) ? $lengths[$field_name] : 32;
                    $fields[$field_name]['length'] = $length;
                }

                $default = array_key_exists($field_name, $defaults) ? $defaults[$field_name] : '';
                if ($required || array_key_exists($field_name, $defaults)) {
                    $fields[$field_name]['default'] = $default;
                }

                // check if not null
                if ($required) {
                    $fields[$field_name]['notnull'] = true;
                    // Sequences
                    if ($required === 'seq') {
                        if ($fields[$field_name]['type'] == 'integer' && $use_auto_increment) {
                            $fields[$field_name]['autoincrement'] = true;
                            $fields[$field_name]['default'] = 0;
                        } else {
                            $sequences[$obj->prefix . $obj->alias[$table_name]] = array(
                                'on' => array(
                                    'table' => $obj->prefix . $obj->alias[$table_name],
                                    'field' => $field_name,
                                )
                            );

                            $table_indexes[$table_name.'_'.$field_name] = array(
                                'fields' => array(
                                    $field_name => true,
                                ),
                                'primary' => true
                            );
                        }
                    // Generate indexes
                    } elseif (is_string($required)) {
                        $index_name = $table_name.'_'.$required . '_i';
                        $table_indexes[$index_name]['fields'][$field_name] = true;
                        $table_indexes[$index_name]['unique'] = true;
                    }
                } else {
                    $fields[$field_name]['notnull'] = ($required === false);
                }
            }
            $tables[$obj->prefix . $obj->alias[$table_name]]['fields'] = $fields;
            $tables[$obj->prefix . $obj->alias[$table_name]]['indexes'] = $table_indexes;
        }

        $definition = array(
            'name' => '<variable>database</variable>',
            'create' => '<variable>create</variable>',
            'overwrite' => '<variable>overwrite</variable>',
            'tables' => $tables,
            'sequences' => $sequences,
        );

        return LiveUser_Misc_Schema_Install::writeSchema($definition, $file);
    }

    /**
     * Takes a given definition array and writes it as xml to a file
     *
     * @param array schema definition
     * @return bool|PEAR_Error true on success or a PEAR_Error on error
     *
     * @access public
     */
    function writeSchema($definition, $file)
    {
        require_once 'MDB2/Schema/Writer.php';
        $writer = new MDB2_Schema_Writer();
        $arguments = array(
            'output_mode' => 'file',
            'output' => $file,
        );
        return $writer->dumpDatabase($definition, $arguments);
    }

    /**
     * Install a schema file into the database
     *
     * @param object LiveUser storage instance
     * @param string name of the file into which the xml schema should be written
     * @param array key-value pairs with keys being variable names and values being the variable values
     * @param bool determines if the database should be created or not
     * @param array MDB2_Schema::connect() options
     * @param bool determines if the database should be created or not
     * @param bool determines if the old schema file should be unlinked first
     * @param bool determines if the disable_query option should be set in MDB2
     * @return bool|PEAR_Error true on success or a PEAR_Error on error
     *
     * @access public
     */
    function installSchema($obj, $file, $variables = array(), $create = true,
        $options = array(), $overwrite = false, $disable_query = false)
    {
        $dsn = array();
        if (is_a($obj->dbc, 'DB_Common')) {
            $dsn = $obj->dbc->dsn;
            $options['seqcol_name'] = 'id';
        } elseif (is_a($obj->dbc, 'PDO')) {
            $dsn = LiveUser_Misc_Schema_Install::parsePDODSN($obj->dbc->dsn);
            $dsn['username'] = array_key_exists('username', $obj->dbc->options)
                ? $obj->dbc->options['username'] : '';
            $dsn['password'] = array_key_exists('password', $obj->dbc->options)
                ? $obj->dbc->options['password'] : '';
            $options['seqname_format'] = '%s';
        } elseif (MDB2::isConnection($obj->dbc)) {
            $dsn = $obj->dbc->getDSN('array');
        }

        $file_old = $file.'.'.$dsn['hostspec'].'.'.$dsn['database'].'.old';
        $variables['create'] = (int)$create;
        $variables['overwrite'] = (int)$overwrite;
        $variables['database'] = $dsn['database'];
        unset($dsn['database']);

        $manager =& MDB2_Schema::factory($dsn, $options);
        if (PEAR::isError($manager)) {
        return $manager;
        }

        if ($overwrite && file_exists($file_old)) {
            unlink($file_old);
        }
        $result = $manager->updateDatabase($file, $file_old, $variables, $disable_query);

        $debug = $manager->db->getOption('debug');
        if ($debug && !PEAR::isError($debug)) {
            echo('Debug messages<br>');
            echo($manager->db->getDebugOutput().'<br>');
        }
        $manager->disconnect();
        return $result;
    }
}

?>
