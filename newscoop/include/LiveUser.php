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
 * @author   Markus Wolff <wolff@21st.de>
 * @author   Helgi Þormar Þorbjörnsson <dufuz@php.net>
 * @author   Lukas Smith <smith@pooteeweet.org>
 * @author   Arnaud Limbourg <arnaud@php.net>
 * @author   Pierre-Alain Joye  <pajoye@php.net>
 * @author   Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version CVS: $Id: LiveUser.php 275349 2009-02-08 13:02:33Z clockwerx $
 * @link http://pear.php.net/LiveUser
 */

/**
 * Include PEAR_ErrorStack
 * and Event_Dispatcher classes
 */
require_once 'PEAR.php';
require_once 'PEAR/ErrorStack.php';
require_once 'Event/Dispatcher.php';

/**#@+
 * Error related constants definition
 *
 * @var int
 */
define('LIVEUSER_ERROR',                        -1);
define('LIVEUSER_ERROR_NOT_SUPPORTED',          -2);
define('LIVEUSER_ERROR_CONFIG',                 -3);
define('LIVEUSER_ERROR_MISSING_DEPS',           -4);
define('LIVEUSER_ERROR_COOKIE',                 -7);
define('LIVEUSER_ERROR_MISSING_FILE',           -8);
define('LIVEUSER_ERROR_FAILED_INSTANTIATION',   -9);
define('LIVEUSER_ERROR_INIT_ERROR',            -10);
define('LIVEUSER_ERROR_MISSING_CLASS',         -11);
define('LIVEUSER_ERROR_WRONG_CREDENTIALS',     -12);
define('LIVEUSER_ERROR_UNKNOWN_EVENT',         -13);
define('LIVEUSER_ERROR_NOT_CALLABLE',          -14);
define('LIVEUSER_ERROR_SESSION_STARTED',       -15);
/**#@-*/

/**#@+
 * Statuses of the current object.
 *
 * @see LiveUser::getStatus
 * @var int
 */
define('LIVEUSER_STATUS_OK',              1);
define('LIVEUSER_STATUS_IDLED',          -1);
define('LIVEUSER_STATUS_EXPIRED',        -2);
define('LIVEUSER_STATUS_ISINACTIVE',     -3);
define('LIVEUSER_STATUS_PERMINITERROR',  -4);
define('LIVEUSER_STATUS_AUTHINITERROR',  -5);
define('LIVEUSER_STATUS_UNKNOWN',        -6);
define('LIVEUSER_STATUS_AUTHNOTFOUND',   -7);
define('LIVEUSER_STATUS_LOGGEDOUT',      -8);
define('LIVEUSER_STATUS_AUTHFAILED',     -9);
define('LIVEUSER_STATUS_UNFROZEN',      -10);
define('LIVEUSER_STATUS_EMPTY_HANDLE',  -11);
/**#@-*/

/**
 * The higest possible right level.
 *
 * Levels are only used in the complex container.
 *
 * @var int
 */
define('LIVEUSER_MAX_LEVEL', 3);

/**#@+
 * Usertypes
 *
 * @var int
 */
/**
 * lowest user type id
 */
define('LIVEUSER_ANONYMOUS_TYPE_ID',   0);
/**
 * User type id
 * It is the highest user type id
 */
define('LIVEUSER_USER_TYPE_ID',        1);
/**
 * lowest admin type id
 */
define('LIVEUSER_ADMIN_TYPE_ID',       2);
/**
 * look up area admin areas to determine which rights are automatically granted
 */
define('LIVEUSER_AREAADMIN_TYPE_ID',   3);
/**
 * from this admin level on all rights are automatically granted
 */
define('LIVEUSER_SUPERADMIN_TYPE_ID',  4);
/**
 * higest admin type id
 */
define('LIVEUSER_MASTERADMIN_TYPE_ID', 5);
/**#@-*/

/**#@+
 * Section types
 *
 * @var int
 */
define('LIVEUSER_SECTION_APPLICATION',  1);
define('LIVEUSER_SECTION_AREA',         2);
define('LIVEUSER_SECTION_GROUP',        3);
define('LIVEUSER_SECTION_RIGHT',        4);
/**#@-*/

// 60 * 60 * 24 == number of seconds in a day
define('LIVEUSER_DAY_SECONDS', 86400);

// 60 * 60 * 24 * 365 * 30 == number of seconds between 1970 and about 2000
define('LIVEUSER_COOKIE_DELETE_TIME', 946080000);

/**
 * This is a manager class for a user login system using the LiveUser
 * class. It creates a LiveUser object, takes care of the whole login
 * process and stores the LiveUser object in a session.
 *
 * You can also configure this class to try to connect to more than
 * one server that can store user information - each server requiring
 * a different backend class.
 *
 * An example would be to create a login
 * system for a live website that first queries the local database and
 * if the requested user is not found, it tries to find it in your
 * company's LDAP server. It means you don't have to create several
 * user accounts for your employees so that they can access closed
 * sections of your website - everyone can use one account.
 *
 * NOTE: No browser output may be made before using this class, because
 * it will try to send HTTP headers such as cookies and redirects.
 *
 * Requirements:
 * - Should run on PHP version 4.2.0 (required for PEAR_Errorstack or higher,
 *   tested only from 4.2.1 onwards
 *
 * Thanks to:
 * Bjoern Schotte, Kristian Koehntopp, Antonio Guerra
 *
 * @category authentication
 * @package  LiveUser
 * @author   Markus Wolff       <wolff@21st.de>
 * @author   Bjoern Kraus       <krausbn@php.net>
 * @author   Lukas Smith        <smith@pooteeweet.org>
 * @author   Pierre-Alain Joye  <pajoye@php.net>
 * @author   Arnaud Limbourg    <arnaud@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser
{
    /**
     * LiveUser options set in the configuration file.
     *
     * @var     array
     * @access  private
     */
    var $_options = array(
        'debug' => false,
        'session'  => array(
            'name'    => 'PHPSESSID',
            'varname' => 'ludata',
            'force_start' => true,
        ),
        'session_save_handler'  => false,
        'session_cookie_params' => false,
        'cache_perm' => false,
        'login' => array(
            'force'   => false,
            'regenid' => false
        ),
        'logout' => array(
            'destroy' => true
        )
    );

    /**
     * The auth container object.
     *
     * @var    object
     * @access private
     */
    var $_auth = null;

    /**
     * The permission container object.
     *
     * @var    object
     * @access private
     */
    var $_perm = null;

    /**
     * Nested array with the auth containers that shall be queried for user information.
     * Format:
     * <code>
     * array('name' => array("option1" => "value", ....))
     * </code>
     * Typical options are:
     * <ul>
     * - server: The adress of the server being queried (ie. "localhost").
     * - handle: The user name used to login for the server.
     * - password: The password used to login for the server.
     * - database: Name of the database containing user information (this is
     *   usually used only by RDBMS).
     * - baseDN: Obviously, this is what you need when using an LDAP server.
     * - connection: Present only if an existing connection shall be used. This
     *   contains a reference to an already existing connection resource or object.
     * - type: The container type. This option must always be present, otherwise
     *   the LiveUser class cannot include the correct container class definition.
     * - name: The name of the auth container. You can freely define this name,
     *   it can be used from within the permission container to see from which
     *   auth container a specific user is coming from.
     *</ul>
     *
     * @var    array
     * @access private
     */
    var $_authContainers = array();

    /**
     * Array of settings the permission container will use to retrieve
     * the user rights.
     * If set to false, no permission container will be used.
     * If that is the case, all calls to checkRight() will return false.
     * The array element 'type' must be present so the LiveUser class can
     * include the correct class definition (example: "DB_Complex").
     *
     * @var    bool|array
     * @access private
     */
    var $_permContainer = false;

    /**
     * Current status of the LiveUser object.
     *
     * @var    string
     * @access private
     * @see    LIVEUSER_STATUS_* constants
     */
    var $_status = LIVEUSER_STATUS_UNKNOWN;

    /**
     * Error stack
     *
     * @var    PEAR_ErrorStack
     * @access private
     */
    var $stack = null;

    /**
     * PEAR::Log object
     * used for error logging by ErrorStack.
     *
     * @var    Log
     * @access public
     */
    var $log = null;

    /**
     * Error codes to message mapping array.
     *
     * @var    array
     * @access private
     */
    var $_errorMessages = array(
        LIVEUSER_ERROR                        => 'Unknown error',
        LIVEUSER_ERROR_NOT_SUPPORTED          => 'Feature not supported by the container: %feature%',
        LIVEUSER_ERROR_CONFIG                 => 'There is an error in the configuration parameters',
        LIVEUSER_ERROR_MISSING_DEPS           => 'Missing package depedencies: %msg%',
        LIVEUSER_ERROR_COOKIE                 => 'There was an error processing the Remember Me cookie',
        LIVEUSER_ERROR_MISSING_FILE           => 'The file %file% is missing',
        LIVEUSER_ERROR_FAILED_INSTANTIATION   => 'Cannot instantiate class %class%',
        LIVEUSER_ERROR_INIT_ERROR             => 'Container was not initialized properly: %container%',
        LIVEUSER_ERROR_MISSING_CLASS          => 'Class %class% does not exist in file %file%',
        LIVEUSER_ERROR_WRONG_CREDENTIALS      => 'The handle and/or password you submitted are not known',
        LIVEUSER_ERROR_UNKNOWN_EVENT          => 'The event %event% is not known',
        LIVEUSER_ERROR_NOT_CALLABLE           => 'Callback %callback% is not callable',
        LIVEUSER_ERROR_SESSION_STARTED        => 'The session cannot be started because the output already begun (i.e. headers were sent)'
    );

    /**
     * Stores the event dispatcher which
     * handles notifications.
     *
     * @var    Event_Dispatcher
     * @access protected
     */
    var $dispatcher = null;

    /**
     * Constructor. Use the factory or singleton methods.
     *
     * @param  bool|object $debug   Boolean that indicates if a log instance
     *                              should be created or an instance of a class
     *                              that implements the PEAR:Log interface.
     * @return void
     * @access protected
     * @see    LiveUser::factory
     * @see    LiveUser::singleton
     */
    function LiveUser(&$debug)
    {
        $this->stack = &PEAR_ErrorStack::singleton('LiveUser');

        if ($debug) {
            $log = LiveUser::PEARLogFactory($debug);
            if ($log) {
                $this->log = $log;
                $this->stack->setLogger($this->log);
            }
        }

        $this->stack->setErrorMessageTemplate($this->_errorMessages);

        $this->dispatcher = Event_Dispatcher::getInstance();
    }

    /**
     * Returns an instance of the LiveUser class.
     *
     * This array contains private options defined by
     * the following associative keys:
     *
     * <code>
     *
     * array(
     *  'debug' => false/true or an instance of a class that implements the PEAR::Log interface
     *  'session'  => array(
     *      'name'    => 'liveuser session name',
     *      'varname' => 'liveuser session var name'
     *  ),
     * // The session_save_handler options are optional. If they are specified,
     * // session_set_save_handler() will be called with the parameters
     *  'session_save_handler' => array(
     *      'open'    => 'name of the open function/method',
     *      'close'   => 'name of the close function/method',
     *      'read'    => 'name of the read function/method',
     *      'write'   => 'name of the write function/method',
     *      'destroy' => 'name of the destroy function/method',
     *      'gc'      => 'name of the gc function/method',
     *  ),
     * // The session_cookie_params options are optional. If they are specified,
     * // session_set_cookie_params() will be called with the parameters
     *  'session_cookie_params' => array(
     *      'lifetime' => 'Cookie lifetime in days',
     *      'path'     => 'Cookie path',
     *      'domain'   => 'Cookie domain',
     *      'secure'   => 'Cookie send only over secure connections',
     *      'httponly' => 'HHTP only cookie, PHP 5.2.0+ only',
     *  ),
     * 'cache_perm' => if the permission data should be cached inside the session
     *  'login' => array(
     *      'force'    => 'Should the user be forced to login'
     *      'regenid'  => 'Should the session be regenerated on login'
     *  ),
     *  'logout' => array(
     *      'destroy'  => 'Whether to destroy the session on logout' false or true
     *  ),
     * // The cookie options are optional. If they are specified, the Remember Me
     * // feature is activated.
     *  'cookie' => array(
     *      'name'     => 'Name of Remember Me cookie',
     *      'lifetime' => 'Cookie lifetime in days',
     *      'path'     => 'Cookie path',
     *      'domain'   => 'Cookie domain',
     *      'secret'   => 'Secret key used for cookie value encryption',
     *      'savedir'  => '/absolute/path/to/writeable/directory' // No trailing slash (/) !
     *      'secure'   => 'Cookie send only over secure connections',
     *      'httponly' => 'HHTP only cookie, PHP 5.2.0+ only',
     *  ),
     *  'authContainers' => array(
     *      'name' => array(
     *            'type'            => 'auth container name',
     *            'expireTime'      => 'maximum lifetime of a session in seconds',
     *            'idleTime'        => 'maximum amount of time between two request',
     *            'passwordEncryptionMode'=> 'what encryption method to use',
     *            'secret'                => 'secret to use in password encryption',
     *            'storage' => array(
     *                'dbc' => 'db connection object, use this or dsn',
     *                'dsn' => 'database dsn, use this or connection',
     *                'handles' => 'array of handle fields to find a user on login, a user
     *                  can login with his username, email or any field you set here;
     *                  works with DB, MDB, MDB2 and PDO containers',
     *           ),
     *           'externalValues' => array(
     *                  'values'      => 'reference to an array',
     *                  'keysToCheck' => 'array of keys to check in the array passed'
     *           ),
     *      ),
     *  ),
     *  'permContainer' => array(
     *      'type'     => 'perm container name',
     *      'storage'  => array(
     *          'storage container name' => array(
     *              'dbc' => 'db connection object, use this or dsn',
     *              'dsn'        => 'database dsn, use this or connection',
     *              'prefix'    => 'table prefix'
     *              'tables'    => 'array containing additional tables or fields in existing tables',
     *              'fields'    => 'array containing any additional or non-default field types',
     *              'alias'     => 'array containing any additional or non-default field alias',
     *              'force_seq' => 'if the use of (emulated) sequences should forced instead of using autoincrement where applicable',
     *          ),
     *      ),
     *  ),
     *
     * </code>
     *
     * Other options in the configuration file relative to
     * the Auth and Perm containers depend on what the
     * containers expect. Refer to the Containers documentation.
     * The examples for containers provided are just general
     * do not reflect all the options for all containers.
     *
     * @param  array      Config array to configure.
     * @return LiveUser   Returns an object of either LiveUser or false on error
     *                    if so use LiveUser::getErrors() to get the errors
     *
     * @access public
     * @see    LiveUser::getErrors
     */
    function &factory(&$conf)
    {
        $debug = false;
        if (array_key_exists('debug', $conf)) {
            $debug = $conf['debug'];
        }

        $obj = new LiveUser($debug);

        if (is_array($conf)) {
            $obj->readConfig($conf);
        }

        return $obj;
    }

    /**
     * This uses the singleton pattern, making sure you have one and
     * only instance of the class.
     *
     * <b>In PHP4 you MUST call this method with the
     *  $var = &LiveUser::singleton() syntax.
     * Without the ampersand (&) in front of the method name, you will not get
     * a reference, you will get a copy.</b>
     *
     * @param  array    Config array to configure.
     * @param  string   Signature by which the given instance can be referenced later
     * @return LiveUser Returns an object of either LiveUser or false on failure
     *
     * @access public
     * @see    LiveUser::factory
     * @see    LiveUser::getErrors
     */
    function &singleton(&$conf, $signature = null)
    {
        static $instances;
        if (!isset($instances)) {
            $instances = array();
        }

        if (is_null($signature)) {
            if (empty($instances)) {
                $signature = uniqid('LU');
            } else {
                $signature = key($instances);
            }
        }

        if (!array_key_exists($signature, $instances)) {
            $instances[$signature] = LiveUser::factory($conf);
        }

        return $instances[$signature];
    }

    /**
     * Wrapper method to get errors from the Error Stack.
     *
     * @return array|bool an array of the errors or false if there are no errors
     *
     * @access public
     */
    function getErrors()
    {
        if (is_object($this->stack)) {
            return $this->stack->getErrors();
        }
        return false;
    }

    /**
     * Loads a PEAR class.
     *
     * @param  string   classname to load
     * @param  bool     if errors should be supressed from the stack
     * @return bool  true success or false on failure
     *
     * @access public
     */
    function loadClass($classname, $supress_error = false)
    {
        if (!LiveUser::classExists($classname)) {
            $filename = str_replace('_', '/', $classname).'.php';
            @include_once($filename);
            if (!LiveUser::classExists($classname) && !$supress_error) {
                if (!LiveUser::fileExists($filename)) {
                    $msg = 'File for the class does not exist ' . $classname;
                } else {
                    $msg = 'Parse error in the file for class' . $classname;
                }
                PEAR_ErrorStack::staticPush('LiveUser', LIVEUSER_ERROR_CONFIG,
                    'exception', array(), $msg);
                return false;
            }
        }
        return true;
    }

    /**
     * Creates an instance of an auth container class.
     *
     * @param  array        Array containing the configuration.
     * @param  string       Name of the container we'll be using.
     * @param  string       Prefix of the class that will be used.
     * @return object|false Returns an instance of an auth container
     *                      class or false on error
     *
     * @access public
     */
    function &authFactory(&$conf, $containerName, $classprefix = 'LiveUser_')
    {
        $auth = false;
        $classname = $classprefix.'Auth_' . $conf['type'];
        if (LiveUser::loadClass($classname)) {
            $auth = new $classname();
            if ($auth->init($conf, $containerName) === false) {
                $auth = false;
            }
        }
        return $auth;
    }

    /**
     * Creates an instance of an perm container class.
     *
     * @param  array         Array containing the configuration.
     * @param  string        Prefix of the class that will be used.
     * @return object|false  Returns an instance of a perm container
     *                       class or false on error
     *
     * @access public
     */
    function &permFactory(&$conf, $classprefix = 'LiveUser_')
    {
        $perm = false;
        $classname = $classprefix.'Perm_' . $conf['type'];
        if (LiveUser::loadClass($classname)) {
            $perm = new $classname();
            if ($perm->init($conf) === false) {
                $perm = false;
            }
        }

        return $perm;
    }

    /**
     * Returns an instance of a storage Container class.
     *
     * @param  array         configuration array to pass to the storage container
     * @param  string        Prefix of the class that will be used.
     * @return object|false  will return an instance of a Storage container
     *                       or false upon error
     *
     * @access protected
     */
    function &storageFactory(&$confArray, $classprefix = 'LiveUser_Perm_')
    {
        end($confArray);
        $key = key($confArray);
        $count = count($confArray);
        $storageName = $classprefix.'Storage_' . $key;
        if (!LiveUser::loadClass($storageName, true)) {
            if ($count <= 1) {
                $storage = false;
                return $storage;
            // if the storage container does not exist try the next one in the stack
            } elseif ($count > 1) {
                // since we are using pass by ref we cannot pop from the original array
                $keys = array_keys($confArray);
                array_pop($keys);
                $newConfArray = array();
                foreach ($keys as $key) {
                    $newConfArray[$key] = $confArray[$key];
                }
                $storage = LiveUser::storageFactory($newConfArray, $classprefix);
                return $storage;
            }
        }
        $storageConf = $confArray[$key];
        $newConfArray = array();
        foreach ($confArray as $keyNew => $foo) {
            if ($key !== $keyNew) {
                $newConfArray[$keyNew] = $confArray[$keyNew];
            }
        }
        $storage = new $storageName();
        if ($storage->init($storageConf, $newConfArray) === false) {
            $storage = false;
        }
        return $storage;
    }

    /**
     * Clobbers two arrays together.
     *
     * Function taken from the user notes of array_merge_recursive function
     * used in LiveUser::readConfig() and may be called statically
     *
     * @param  array        array that should be clobbered
     * @param  array        array that should be clobbered
     * @return array|false  array on success and false on error
     *
     * @access public
     * @author kc@hireability.com
     */
    function arrayMergeClobber($a1, $a2)
    {
        if (!is_array($a1) || !is_array($a2)) {
            return false;
        }
        foreach ($a2 as $key => $val) {
            if (is_array($val) && array_key_exists($key, $a1) && is_array($a1[$key])) {
                $a1[$key] = LiveUser::arrayMergeClobber($a1[$key], $val);
            } else {
                $a1[$key] = $val;
            }
        }
        return $a1;
    }

    /**
     * Checks if a file exists in the include path.
     *
     * @param  string  filename
     * @return bool true success and false on error
     *
     * @access public
     */
    function fileExists($file)
    {
        // safe_mode does notwork with is_readable()
        if (ini_get('safe_mode')) {
            $fp = @fopen($file, 'r', true);
            if (is_resource($fp)) {
                @fclose($fp);
                return true;
            }
        } else {
             $dirs = explode(PATH_SEPARATOR, ini_get('include_path'));
             foreach ($dirs as $dir) {
                 if (is_readable($dir . DIRECTORY_SEPARATOR . $file)) {
                     return true;
                 }
            }
        }
        return false;
    }

    /**
     * Checks if a class exists without triggering __autoload
     *
     * @param  string  classname
     * @return bool true success and false on error
     *
     * @access public
     */
    function classExists($classname)
    {
        if (version_compare(phpversion(), "5.0", ">=")) {
            return class_exists($classname, false);
        }
        return class_exists($classname);
    }

    /**
     * Reads the configuration array.
     *
     * @param  array|file  Conf array or file path to configuration
     * @param  string      Name of array containing the configuration
     * @return bool        true on success or false on failure
     *
     * @access public
     * @see    LiveUser::factory
     */
    function readConfig($conf)
    {
        if (array_key_exists('authContainers', $conf)) {
            $this->_authContainers = $conf['authContainers'];
        }
        if (array_key_exists('permContainer', $conf)) {
            $this->_permContainer = $conf['permContainer'];
        }

        $this->_options = LiveUser::arrayMergeClobber($this->_options, $conf);
        if (array_key_exists('cookie', $this->_options) && $this->_options['cookie']) {
            $cookie_default = array(
                'name'     => 'ludata',
                'lifetime' => '365',
                'path'     => '/',
                'domain'   => '',
                'secret'   => 'secret',
                'httponly' => false,
            );
            if (is_array($this->_options['cookie'])) {
                $this->_options['cookie'] =
                    LiveUser::arrayMergeClobber($cookie_default, $this->_options['cookie']);
            } else {
                $this->_options['cookie'] = $cookie_default;
            }
        }

        if (array_key_exists('session_cookie_params', $this->_options) && $this->_options['session_cookie_params']) {
            $session_cookie_params_default = array(
                'httponly' => false,
            );
            if (is_array($this->_options['session_cookie_params'])) {
                $this->_options['session_cookie_params'] =
                    LiveUser::arrayMergeClobber($session_cookie_params_default, $this->_options['cookie']);
            } else {
                $this->_options['session_cookie_params'] = $session_cookie_params_default;
            }
        }

        return true;
    }

    /**
     * Determines if loading of PEAR::Log is necessary.
     *
     * If an object is passed it is returned, otherwise Log is loaded
     * and instantiated.
     *
     * @param  bool|Log Boolean that indicates if a log instance
     *                  should be created or an instance of a class
     *                  that implements the PEAR:Log interface.
     * @return log instance of the given log class
     *
     * @access protected
     */
    function &PEARLogFactory(&$log)
    {
        if (empty($log) || is_object($log)) {
            return $log;
        }

        require_once 'Log.php';
        $log = Log::factory('composite');
        if (!is_a($log, 'Log_composite')) {
            $this->stack->push(
                LIVEUSER_ERROR_CONFIG, 'exception', array(),
                'Could not create Log instance'
            );
            $return = false;
            return $return;
        }
        $conf = array(
            'colors' => array(
                PEAR_LOG_EMERG   => 'red',
                PEAR_LOG_ALERT   => 'orange',
                PEAR_LOG_CRIT    => 'yellowgreen',
                PEAR_LOG_ERR     => 'green',
                PEAR_LOG_WARNING => 'blue',
                PEAR_LOG_NOTICE  => 'indigo',
                PEAR_LOG_INFO    => 'violet',
                PEAR_LOG_DEBUG   => 'black',
            ),
        );
        $winlog = Log::factory('win', 'LiveUser', 'LiveUser', $conf);
        if (!is_a($winlog, 'Log_win')) {
            $this->stack->push(
                LIVEUSER_ERROR_CONFIG, 'exception', array(),
                'Could not create Log "window" instance'
            );
            $return = false;
            return $return;
        }
        $log->addChild($winlog);

        return $log;
    }

    /**
     * Decrypts a password so that it can be compared with the user input.
     * Uses the algorithm defined in the passwordEncryptionMode parameter.
     *
     * @param  string the encrypted password
     * @param  string the encryption mode
     * @return string The decrypted password
     */
    function decryptPW($encryptedPW, $passwordEncryptionMode, $secret)
    {
        if (empty($encryptedPW) && $encryptedPW !== 0) {
            return '';
        }

        $passwordEncryptionMode = strtolower($passwordEncryptionMode);

        if ($passwordEncryptionMode === 'plain') {
            return $encryptedPW;
        }

        if ($passwordEncryptionMode === 'rc4') {
            return LiveUser::cryptRC4($decryptedPW, $secret, false);
        }

        PEAR_ErrorStack::staticPush('LiveUser', LIVEUSER_ERROR_NOT_SUPPORTED, 'error', array(),
            'Could not find the requested decryption function : ' . $passwordEncryptionMode);
        return false;
    }

    /**
     * Encrypts a password for storage in a backend container.
     * Uses the algorithm defined in the passwordEncryptionMode parameter.
     *
     * @param string  password to encrypt
     * @param  string the encryption mode
     * @param  string token to use to encrypt data
     * @return string The encrypted password
     */
    function encryptPW($plainPW, $passwordEncryptionMode, $secret)
    {
        if (empty($plainPW) && $plainPW !== 0) {
            return '';
        }

        $passwordEncryptionMode = strtolower($passwordEncryptionMode);

        if ($passwordEncryptionMode == 'plain') {
            return $plainPW;
        }

        if ($passwordEncryptionMode == 'md5') {
            return md5($plainPW);
        }

        if (extension_loaded('hash') && in_array($passwordEncryptionMode, hash_algos())) {
            return hash($passwordEncryptionMode, $plainPW);
        }

        if ($passwordEncryptionMode == 'rc4') {
            return LiveUser::cryptRC4($plainPW, $secret, true);
        }

        if (function_exists('sha1') && $passwordEncryptionMode == 'sha1') {
            return sha1($plainPW);
        }

        PEAR_ErrorStack::staticPush('LiveUser', LIVEUSER_ERROR_NOT_SUPPORTED, 'error', array(),
            'Could not find the requested encryption function : ' . $passwordEncryptionMode);
        return false;
    }

    /**
     * Creates an instance of the PEAR::Crypt_Rc4 class.
     *
     * @param  string token to use to encrypt data
     * @return Crypt_RC4 returns an instance of the Crypt_RC4 class
     *
     * @access public
     */
    function &cryptRC4Factory($secret)
    {
        $rc4 = false;
        if (LiveUser::loadClass('Crypt_Rc4')) {
            $rc4 = new Crypt_Rc4($secret);
        }
        return $rc4;
    }

    /**
     * Crypts data using mcrypt or userland if not available.
     *
     * @param  string   data
     * @param  string   secret key
     * @param  bool     true if it should be crypted,
     *                  false if it should be decrypted
     * @return string   (de-)crypted data
     *
     * @access public
     */
    function cryptRC4($data, $secret, $crypt = true)
    {
        if (function_exists('mcrypt_module_open')) {
            $td = mcrypt_module_open('tripledes', '', 'ecb', '');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
            mcrypt_generic_init($td, $secret, $iv);
            if ($crypt) {
                $data = mcrypt_generic($td, $data);
            } else {
                $data = mdecrypt_generic($td, $data);
            }
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        } else {
            $rc4 = LiveUser::cryptRC4Factory($secret);
            if (!$rc4) {
                $this->stack->push(
                    LIVEUSER_ERROR_CONFIG, 'exception', array(),
                    'RememberMe feature requires either the mcrypt extension or PEAR::Crypt_RC4'
                );
                return false;
            }
            if ($crypt) {
                $rc4->crypt($data);
            } else {
                $rc4->decrypt($data);
            }
        }

        return $data;
    }

    /**
     * Sets an option after the configuration array has been loaded.
     *
     * You can override a specific option calling this method.
     *
     * @param  string   option name
     * @param  mixed    value for the option
     * @return bool  true on success or false on failure
     *
     * @access public
     * @see    LiveUser::_options
     */
    function setOption($option, $value)
    {
        if (array_key_exists($option, $this->_options)) {
            $this->_options[$option] = $value;
            return true;
        }
        $this->stack->push(LIVEUSER_ERROR_CONFIG, 'exception', array(),
            "unknown option $option");
        return false;
    }

    /**
     * Returns the value of an option from the configuration array.
     *
     * @param  string  option name
     * @return mixed   the option value or false on failure
     *
     * @access public
     */
    function getOption($option)
    {
        if (array_key_exists($option, $this->_options)) {
            return $this->_options[$option];
        }
        $this->stack->push(LIVEUSER_ERROR_CONFIG, 'exception', array(),
            "unknown option $option");
        return false;
    }

    /**
     * Sets the session handler and name and starts the session if headers have
     * not been send yet.
     *
     * @return bool  true on success or false on failure
     *
     * @access private
     */
    function _startSession()
    {
        // set session save handler if needed
        if ($this->_options['session_save_handler']) {
            session_set_save_handler(
                $this->_options['session_save_handler']['open'],
                $this->_options['session_save_handler']['close'],
                $this->_options['session_save_handler']['read'],
                $this->_options['session_save_handler']['write'],
                $this->_options['session_save_handler']['destroy'],
                $this->_options['session_save_handler']['gc']
            );
        }
        if ($this->_options['session_cookie_params']) {
            session_set_cookie_params((
                (LIVEUSER_DAY_SECONDS * $this->_options['session_cookie_params']['lifetime'])),
                $this->_options['session_cookie_params']['path'],
                $this->_options['session_cookie_params']['domain'],
                $this->_options['session_cookie_params']['secure'],
                $this->_options['session_cookie_params']['httponly']);
        }
        // Set the name of the current session
        session_name($this->_options['session']['name']);
        // Check if we can safely start the session
        if (headers_sent()) {
            $this->stack->push(
                LIVEUSER_ERROR_SESSION_STARTED, 'exception'
            );
            return;
        }
        // If there's no session yet, start it now
        @session_start();

        return true;
    }

    /**
     * Tries to retrieve the auth object from session and checks possible timeouts.
     *
     * @return bool  true if init process well, false if something went wrong.
     *
     * @access public
     */
    function init()
    {
        if ($this->_options['session']['force_start']) {
            $this->_startSession();
        }

        // Try to fetch auth object from session
        $isReturningUser = $this->_unfreeze();

        // current timestamp
        $now = time();

        if ($this->isLoggedIn()) {
            if ($isReturningUser) {
                // Check if authentication session is expired.
                if ($this->getProperty('expireTime') > 0
                    && ($this->getProperty('currentLogin') + $this->getProperty('expireTime')) < $now
                ) {
                    $this->logout(false);
                    $this->_status = LIVEUSER_STATUS_EXPIRED;
                    $this->dispatcher->post($this, 'onExpired');
                // Check if maximum idle time is reached.
                } elseif ($this->getProperty('idleTime') > 0
                    && array_key_exists('idle', $_SESSION[$this->_options['session']['varname']])
                    && ($_SESSION[$this->_options['session']['varname']]['idle'] + $this->getProperty('idleTime')) < $now
                ) {
                    $this->logout(false);
                    $this->_status = LIVEUSER_STATUS_IDLED;
                    $this->dispatcher->post($this, 'onIdled');
                }
            }
        }

        // set idle time and status
        if ($this->isLoggedIn()) {
            $_SESSION[$this->_options['session']['varname']]['idle'] = $now;
            $this->_status = LIVEUSER_STATUS_OK;
        // Force user login.
        } elseif ($this->_options['login']['force']) {
            $this->dispatcher->post($this, 'forceLogin');
        }

        return true;
    }

    /**
     * Tries to log the user in by trying all the Auth containers defined
     * in the configuration file until there is a success or a failure.
     *
     * @param  string   handle of the user trying to authenticate
     * @param  string   password of the user trying to authenticate
     * @param  bool  set if remember me is set, requires cookie otion
     * @param  bool|int if the user data should be read using the auth user id
     * @return bool  true on success or false on failure
     *
     * @access public
     */
    function login($handle = '', $passwd = '', $remember = false, $auth_user_id = false)
    {
        if ($remember && $auth_user_id) {
            $this->_status = LIVEUSER_STATUS_AUTHINITERROR;
            $this->stack->push(LIVEUSER_ERROR, 'exception',
                array('msg' => 'Remember me feature is incompatible logging in via the auth_user_id'));
            return false;
        }

        if (empty($handle) && $remember) {
            $result = $this->readRememberCookie();
            if (!is_array($result)) {
                if ($this->_status == LIVEUSER_STATUS_UNKNOWN) {
                    $this->_status = LIVEUSER_STATUS_EMPTY_HANDLE;
                }
                return false;
            }
            $handle = $result['handle'];
            $passwd = $result['passwd'];
        }

        $this->_status = LIVEUSER_STATUS_AUTHFAILED;
        $this->_auth = $this->_perm = null;

        //loop into auth containers
        $containerNames = array_keys($this->_authContainers);
        foreach ($containerNames as $containerName) {
            $auth =& LiveUser::authFactory($this->_authContainers[$containerName], $containerName);
            if ($auth === false) {
                $this->_status = LIVEUSER_STATUS_AUTHINITERROR;
                $this->stack->push(LIVEUSER_ERROR, 'exception',
                    array('msg' => 'Could not instanciate auth container: '.$containerName));
                return false;
            }
            $login = $auth->login($handle, $passwd, $auth_user_id);
            if ($login === false) {
                $this->_status = LIVEUSER_STATUS_AUTHINITERROR;
                $this->stack->push(LIVEUSER_ERROR, 'exception',
                    array('msg' => 'Could not execute login method: '.$containerName));
                return false;
            }
            if ($auth->loggedIn) {
                $this->_auth =& $auth;
                if ($remember) {
                    $this->setRememberCookie($handle, $passwd);
                }
                $this->_status = LIVEUSER_STATUS_OK;
                // Create permission object
                if (is_array($this->_permContainer)) {
                    $perm =& LiveUser::permFactory($this->_permContainer);
                    if ($perm === false) {
                        $this->_status = LIVEUSER_STATUS_PERMINITERROR;
                        $this->stack->push(LIVEUSER_ERROR, 'exception',
                            array('msg' => 'Could not instanciate perm container of type: ' . $this->_permContainer['type']));
                        return false;
                    }
                    if (!$perm->mapUser($auth->getProperty('auth_user_id'), $containerName)) {
                        $this->dispatcher->post($this, 'onFailedMapping');
                    } else {
                        $this->_perm =& $perm;
                    }
                }
                $this->_freeze();
                break;
            } elseif (!is_null($login) && !$auth->getProperty('is_active')) {
                $this->_status = LIVEUSER_STATUS_ISINACTIVE;
                break;
            }
        }

        if (!$this->isLoggedIn()) {
            $this->dispatcher->post($this, 'onFailedLogin');
            return false;
        }

        // user has just logged in
        if (!$this->_options['session']['force_start']) {
            $this->_startSession();
        }
        if ($this->_options['login']['regenid']) {
            session_regenerate_id();
        }
        $this->dispatcher->post($this, 'onLogin');

        return true;
    }

    /**
     * Gets auth and perm container objects back from session and tries
     * to give them an active database/whatever connection again.
     *
     * @return bool true on success or false on failure
     *
     * @access private
     */
    function _unfreeze()
    {
        if (!$this->_options['session']['force_start']) {
            if (!array_key_exists($this->_options['session']['name'], $_REQUEST)) {
                return false;
            }
            $this->_startSession();
        }

        if (isset($_SESSION[$this->_options['session']['varname']])
            && array_key_exists('auth', $_SESSION[$this->_options['session']['varname']])
            && is_array($_SESSION[$this->_options['session']['varname']]['auth'])
            && array_key_exists('auth_name', $_SESSION[$this->_options['session']['varname']])
            && strlen($_SESSION[$this->_options['session']['varname']]['auth_name']) > 0
        ) {
            $containerName = $_SESSION[$this->_options['session']['varname']]['auth_name'];
            $auth =& LiveUser::authFactory($this->_authContainers[$containerName], $containerName);
            if ($auth === false) {
                $this->stack->push(LIVEUSER_ERROR, 'exception',
                    array('msg' => 'Could not instanciate auth container: '.$containerName));
                return false;
            }
            if ($auth->unfreeze($_SESSION[$this->_options['session']['varname']]['auth'])) {
                $auth->containerName = $_SESSION[$this->_options['session']['varname']]['auth_name'];
                $this->_auth = &$auth;
                if (array_key_exists('perm', $_SESSION[$this->_options['session']['varname']])
                    && $_SESSION[$this->_options['session']['varname']]['perm']
                ) {
                    $perm =& LiveUser::permFactory($this->_permContainer);
                    if ($perm === false) {
                        $this->stack->push(LIVEUSER_ERROR, 'exception',
                            array('msg' => 'Could not instanciate perm container of type: ' . $this->_permContainer));
                        return $perm;
                    }
                    if ($this->_options['cache_perm']) {
                        $result = $perm->unfreeze($this->_options['session']['varname']);
                    } else {
                        $result = $perm->mapUser($auth->getProperty('auth_user_id'), $auth->containerName);
                    }
                    if ($result) {
                        $this->_perm = &$perm;
                    }
                }
                $this->_status = LIVEUSER_STATUS_UNFROZEN;
                $this->dispatcher->post($this, 'onUnfreeze');
                return true;
            }
        }

        return false;
    }

    /**
     * Stores all properties in the session.
     *
     * @return  bool true on sucess or false on failure
     *
     * @access  private
     */
    function _freeze()
    {
        if (is_a($this->_auth, 'LiveUser_Auth_Common') && $this->isLoggedIn()) {
            // Bind objects to session
            $_SESSION[$this->_options['session']['varname']] = array();
            $_SESSION[$this->_options['session']['varname']]['auth'] = $this->_auth->freeze();
            $_SESSION[$this->_options['session']['varname']]['auth_name'] = $this->_auth->containerName;
            if (is_a($this->_perm, 'LiveUser_Perm_Simple')) {
                $_SESSION[$this->_options['session']['varname']]['perm'] = true;
                if ($this->_options['cache_perm']) {
                     $this->_perm->freeze($this->_options['session']['varname']);
                }
            }
            return true;
        }
        $this->stack->push(LIVEUSER_ERROR_CONFIG, 'exception', array(),
            'No data available to store inside session');
        return false;
    }

    /**
     * Properly disconnect resources in the active container.
     *
     * @return  bool true on success or false on failure
     *
     * @access public
     */
    function disconnect()
    {
        if (is_a($this->_auth, 'LiveUser_Auth_Common')) {
            $result = $this->_auth->disconnect();
            if ($result === false) {
                return false;
            }
            $this->_auth = null;
        }
        if (is_a($this->_perm, 'LiveUser_Perm_Simple')) {
            $result = $this->_perm->disconnect();
            if ($result === false) {
                return false;
            }
            $this->_perm = null;
        }
        return true;
    }

    /**
     * If cookies are allowed, this method checks if the user wanted
     * a cookie to be set so he doesn't have to enter handle and password
     * for his next login. If true, it will set the cookie.
     *
     * @param  string   handle of the user trying to authenticate
     * @param  string   password of the user trying to authenticate
     * @return bool  true if the cookie can be set, false otherwise
     *
     * @access public
     */
    function setRememberCookie($handle, $passwd)
    {
        if (!array_key_exists('cookie', $this->_options)) {
            return false;
        }

        $store_id = md5($handle . $passwd);

        $dir = $this->_options['cookie']['savedir'];
        $file = $dir . '/' . $store_id . '.lu';

        if (!is_writable($dir)) {
            $this->stack->push(LIVEUSER_ERROR_CONFIG, 'exception', array(),
                'Cannot create file, please check path and permissions');
            return false;
        }

        $fh = @fopen($file, 'wb');
        if (!$fh) {
            $this->stack->push(LIVEUSER_ERROR_CONFIG, 'exception', array(),
                'Cannot open file for writting');
            return false;
        }

        $passwd_id = md5($passwd);
        $crypted_data = LiveUser::cryptRC4(
            serialize(array($passwd_id, $passwd)),
            $this->_options['cookie']['secret'],
            true
        );

        $write = fwrite($fh, $crypted_data);
        fclose($fh);
        if (!$write) {
            $this->stack->push(LIVEUSER_ERROR_CONFIG, 'exception', array(),
                'Cannot save cookie data');
            return false;
        }

        // PHP complains if you are on a lower version than PHP 5.2
        if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
            $setcookie = setcookie(
                $this->_options['cookie']['name'],
                $store_id . $passwd_id . $handle,
                (time() + (LIVEUSER_DAY_SECONDS * $this->_options['cookie']['lifetime'])),
                $this->_options['cookie']['path'],
                $this->_options['cookie']['domain'],
                $this->_options['cookie']['secure'],
                $this->_options['cookie']['httponly']
            );
        } else {
            $setcookie = setcookie(
                $this->_options['cookie']['name'],
                $store_id . $passwd_id . $handle,
                (time() + (LIVEUSER_DAY_SECONDS * $this->_options['cookie']['lifetime'])),
                $this->_options['cookie']['path'],
                $this->_options['cookie']['domain'],
                $this->_options['cookie']['secure']
            );
        }

        if (!$setcookie) {
            @unlink($file);
            $this->stack->push(LIVEUSER_ERROR_CONFIG, 'exception', array(),
                'Unable to set cookie');
            return false;
        }

        return true;
    }

    /**
     * Handles the retrieval of the login data from the rememberMe cookie.
     *
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function readRememberCookie()
    {
        if (!array_key_exists('cookie', $this->_options)
            || !array_key_exists($this->_options['cookie']['name'], $_COOKIE)
        ) {
            return false;
        }

        if (strlen($_COOKIE[$this->_options['cookie']['name']]) < 65
            || preg_match('/[^a-z0-9]/i', substr($_COOKIE[$this->_options['cookie']['name']], 0, 64))
        ) {
            $this->deleteRememberCookie();
        }
        $cookieData = $_COOKIE[$this->_options['cookie']['name']];

        $store_id = substr($cookieData, 0, 32);
        $passwd_id = substr($cookieData, 32, 32);
        $handle = substr($cookieData, 64);

        $dir = $this->_options['cookie']['savedir'];

        $fh = @fopen($dir . '/' . $store_id . '.lu', 'rb');
        if (!$fh) {
            $this->deleteRememberCookie();
            $this->stack->push(LIVEUSER_ERROR_CONFIG, 'exception', array(),
                'Cannot open file for reading');
            return false;
        }

        $fields = fread($fh, 4096);
        fclose($fh);
        if (!$fields) {
            $this->deleteRememberCookie();
            $this->stack->push(LIVEUSER_ERROR_CONFIG, 'exception', array(),
                'Cannot read file');
            return false;
        }

        $serverData = @unserialize(
            LiveUser::cryptRC4($fields, $this->_options['cookie']['secret'], false)
        );

        if (!is_array($serverData) || count($serverData) != 2) {
            $this->deleteRememberCookie();
            $this->stack->push(LIVEUSER_ERROR_COOKIE, 'exception', array(),
                'Incorrect array structure');
            return false;
        }

        if ($serverData[0] != $passwd_id) {
            // Delete cookie if it's not valid, keeping it messes up the
            // authentication process
            $this->deleteRememberCookie();
            $this->stack->push(LIVEUSER_ERROR_COOKIE, 'error', array(),
                'Passwords hashes do not match in cookie in LiveUser::readRememberMeCookie()');
            return false;
        }

        return array('handle' => $handle, 'passwd' => $serverData[1]);
    }

    /**
     * Deletes the rememberMe cookie.
     *
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function deleteRememberCookie()
    {
        if (!array_key_exists('cookie', $this->_options)
            || !array_key_exists($this->_options['cookie']['name'], $_COOKIE)
        ) {
            return false;
        }

        if (preg_match('/[^a-z0-9]/i', substr($_COOKIE[$this->_options['cookie']['name']], 0, 32))) {
            $this->stack->push(LIVEUSER_ERROR_COOKIE, 'error', array(),
                'Malformed rememberme cookie identifer in LiveUser::deleteRememberCookie()');
            return false;
        }
        $cookieData = $_COOKIE[$this->_options['cookie']['name']];

        $store_id = substr($cookieData, 0, 32);
        @unlink($this->_options['cookie']['savedir'] . '/'.$store_id.'.lu');

        unset($_COOKIE[$this->_options['cookie']['name']]);
        setcookie($this->_options['cookie']['name'],
            false,
            LIVEUSER_COOKIE_DELETE_TIME,
            $this->_options['cookie']['path'],
            $this->_options['cookie']['domain'],
            $this->_options['cookie']['secure']
        );

        return true;
    }

    /**
     * This logs the user out and destroys the session object if the
     * configuration option is set.
     *
     * @param bool  set to false if no events should be fired b yhte logout
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function logout($direct = true)
    {
        $this->_status = LIVEUSER_STATUS_LOGGEDOUT;

        if ($direct) {
            // trigger event 'onLogout' as replacement for logout callback function
            $this->dispatcher->post($this, 'onLogout');
            // If there's a cookie and the session hasn't idled or expired, kill that one too...
            $this->deleteRememberCookie();
        }

        // If the session should be destroyed, do so now...
        if ($this->_options['logout']['destroy']) {
            session_unset();
            session_destroy();
            if ($this->_options['session']['force_start']) {
                $this->_startSession();
            }
        } elseif (array_key_exists($this->_options['session']['varname'], $_SESSION)) {
            unset($_SESSION[$this->_options['session']['varname']]);
        }

        $this->disconnect();

        if ($direct) {
            // trigger event 'postLogout', can be used to do a redirect
            $this->dispatcher->post($this, 'postLogout');
        }

        return true;
    }

    /**
     * Wrapper method for the permission object's own checkRight method.
     * Use this method to determine if a user has a given right or set
     * of rights.
     *
     * @param  array|int   A right id or an array of rights.
     * @return int|false  level if the user has the right/rights false if not
     *
     * @access public
     */
    function checkRight($rights)
    {
        if (is_null($rights)) {
            return LIVEUSER_MAX_LEVEL;
        }

        if (is_a($this->_perm, 'LiveUser_Perm_Simple')) {
            if (is_array($rights)) {
                // assume user has the right in order to have min() work
                $hasright = LIVEUSER_MAX_LEVEL;
                foreach ($rights as $currentright) {
                    $level = $this->_perm->checkRight($currentright);
                    if (!$level) {
                        return false;
                    }
                    $hasright = min($hasright, $level);
                }
                return $hasright;
            } else {
                return $this->_perm->checkRight($rights);
            }
        } elseif ($rights === 0 && is_a($this->_auth, 'LiveUser_Auth_Common')) {
            return LIVEUSER_MAX_LEVEL;
        }

        return false;
    }

    /**
     * Wrapper method for the permission object's own checkRight and checkLevel methods
     *
     * @param  array|int  A right id or an array of rights.
     * @param  array|int  Id or array of Ids of the owner of the
                          ressource for which the right is requested.
     * @param  array|int  Id or array of Ids of the group of the
     *                    ressource for which the right is requested.
     * @return bool    true on success or false on failure
     *
     * @access public
     */
    function checkRightLevel($rights, $owner_user_id, $owner_group_id)
    {
        $level = $this->checkRight($rights);
        if (is_a($this->_perm, 'LiveUser_Perm_Complex')) {
            $level = $this->_perm->checkLevel($level, $owner_user_id, $owner_group_id);
        }

        return (bool)$level;
    }

    /**
     * Wrapper method for the permission object's own checkGroup method.
     *
     * @param  array|int  A group id or an array of groups.
     * @return bool    true on success or false on failure
     *
     * @access public
     */
    function checkGroup($groups)
    {
        if (is_null($groups)) {
            return true;
        }

        if (is_a($this->_perm, 'LiveUser_Perm_Medium')) {
            if (is_array($groups)) {
                foreach ($groups as $group) {
                    if (!$this->_perm->checkGroup($group)) {
                        return false;
                    }
                }
                return true;
            } else {
                return $this->_perm->checkGroup($groups);
            }
        }

        return false;
    }

    /**
     * Checks if a user is logged in.
     *
     * @return bool true if user is logged in, false if not
     *
     * @access public
     */
    function isLoggedIn()
    {
        if (!is_a($this->_auth, 'LiveUser_Auth_Common')) {
            return false;
        }

        return $this->_auth->loggedIn;
    }

    /**
     * Function that determines if the user exists but hasn't yet been declared
     * "active" by an administrator.
     *
     * Use this to check if this was the reason
     * why a user was not able to login.
     * true ==  user account is NOT active
     * false == user account is active
     *
     * @return bool true if the user account is *not* active
     *                 false if the user account *is* active
     *
     * @access public
     */
    function isInactive()
    {
        return $this->_status == LIVEUSER_STATUS_ISINACTIVE;
    }

    /**
     * Wrapper method to access properties from the auth and
     * permission containers.
     *
     * @param  string  Name of the property to be returned.
     * @param  string  'auth' or 'perm'
     * @return mixed   a scalarvalue, an object or an array.
     *
     * @access public
     */
    function getProperty($what, $container = 'auth')
    {
        $that = null;
        if ($container == 'auth' && is_a($this->_auth, 'LiveUser_Auth_Common')
            && !is_null($this->_auth->getProperty($what))
        ) {
            $that = $this->_auth->getProperty($what);
        } elseif (is_a($this->_perm, 'LiveUser_Perm_Simple')
            && !is_null($this->_perm->getProperty($what))
        ) {
            $that = $this->_perm->getProperty($what);
        }
        return $that;
    }

    /**
     * Updates the properties of the containers from the original source.
     *
     * @param bool if the auth container should be updated
     * @param bool if the perm container should be updated
     * @return bool true on success and false on failure
     *
     * @access public
     */
    function updateProperty($auth, $perm = null)
    {
        if (!is_a($this->_auth, 'LiveUser_Auth_Common')) {
            $this->stack->push(LIVEUSER_ERROR, 'error', array(),
                'Cannot update container if no auth container instance is available');
            return false;
        }
        if ($auth && !$this->_auth->readUserData(null, null, $this->_auth->getProperty('auth_user_id'))) {
            return false;
        }
        if (is_null($perm)) {
            $perm = is_a($this->_perm, 'LiveUser_Perm_Simple');
        }
        if ($perm) {
            if (!is_a($this->_perm, 'LiveUser_Perm_Simple')) {
                $this->stack->push(LIVEUSER_ERROR, 'error', array(),
                    'Cannot update container if no perm container instance is available');
                return false;
            }
            if (!$this->_perm->mapUser($this->_auth->getProperty('auth_user_id'), $this->_auth->containerName)) {
                return false;
            }
        }
        $this->_freeze();
        return true;
    }

    /**
     * Get the current status.
     *
     * @return int a LIVEUSER_STATUS_* constant
     *
     * @access public
     * @see    LIVEUSER_STATUS_* constants
     */
    function getStatus()
    {
        return $this->_status;
    }

    /**
     * Make a string representation of the object.
     *
     * @return  string  returns a string representation of the class
     *
     * @access private
     */
    function __toString()
    {
        return get_class($this) . ' logged in: ' . ($this->isLoggedIn() ? 'Yes' : 'No');
    }

    /**
     * Return a textual status message for a LiveUser status code.
     *
     * @param   int|array   integer status code,
                                null to get the current status code-message map,
                                or an array with a new status code-message map
     * @return  string  error message
     *
     * @access public
     */
    function statusMessage($value = null)
    {
        // make the variable static so that it only has to do the defining on the first call
        static $statusMessages;

        if (is_array($value)) {
            $statusMessages = $value;
            return true;
        }

        if (!isset($statusMessages)) {
            $statusMessages = array(
                LIVEUSER_STATUS_OK              => 'Authentication OK',
                LIVEUSER_STATUS_IDLED           => 'Maximum idle time is reached',
                LIVEUSER_STATUS_EXPIRED         => 'User session has expired',
                LIVEUSER_STATUS_ISINACTIVE      => 'User account is inactive',
                LIVEUSER_STATUS_PERMINITERROR   => 'Cannot instantiate permission container',
                LIVEUSER_STATUS_AUTHINITERROR   => 'Cannot instantiate the authentication container, this is a configuration problem',
                LIVEUSER_STATUS_AUTHNOTFOUND    => 'Cannot retrieve Auth object from session',
                LIVEUSER_STATUS_UNKNOWN         => 'An undefined error occurred or init() was not called',
                LIVEUSER_STATUS_LOGGEDOUT       => 'User was logged out correctly',
                LIVEUSER_STATUS_AUTHFAILED      => 'Authentication failed, handle/password is probably wrong',
                LIVEUSER_STATUS_UNFROZEN        => 'Object fetched from the session, the user was already logged in',
                LIVEUSER_STATUS_EMPTY_HANDLE    => 'No handle was passed to LiveUser directly or indirectly',
            );
        }

        if (is_null($value)) {
            return $statusMessages;
        }

        // return the textual error message corresponding to the code
        return array_key_exists($value, $statusMessages)
            ? $statusMessages[$value] : $statusMessages[LIVEUSER_STATUS_UNKNOWN];
    }
}
?>
