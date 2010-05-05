<?php

function php_error_handler($errno, $errstr, $errfile, $errline)
{
    if (error_reporting() && $errno != 2048) {
        var_dump('error_msg', "<b>$errfile ($errline)</b><br />$errstr");
    }
}

set_error_handler('php_error_handler');

require_once 'LiveUser/Admin.php';
// Please configure the following file according to your environment

$GLOBALS['_LIVEUSER_DEBUG'] = true;

$db_user = 'root';
$db_pass = '';
$db_host = 'localhost';
$db_name = 'liveuser_admin_test_example1';

$dsn = "mysql://$db_user:$db_pass@$db_host/$db_name";

$backends = array(
    'DB' => array(
        'options' => array()
    ),
    'MDB' => array(
        'options' => array()
    ),
    'MDB2' => array(
        'options' => array(
            'debug' => true,
            'debug_handler' => 'echoQuery',
        )
    )
);

if (!array_key_exists('storage', $_GET)) {
    $storage = 'MDB2';
} elseif (isset($backends[$_GET['storage']])) {
    $storage = strtoupper($_GET['storage']);
} else {
    exit('storage Backend not found.');
}

require_once $storage.'.php';

function echoQuery(&$db, $scope, $message)
{
    Var_Dump::display($scope.': '.$message);
}

$dummy = new $storage;
$db = $dummy->connect($dsn, $backends[$storage]['options']);

if (PEAR::isError($db)) {
    echo $db->getMessage() . ' ' . $db->getUserInfo();
    die();
}

$db->setFetchMode(constant($storage.'_FETCHMODE_ASSOC'));

$conf =
    array(
        'debug' => true,
        'session'  => array(
            'name'     => 'PHPSESSION',
            'varname'  => 'ludata'
        ),
        'login' => array(
            'force'    => false,
        ),
        'logout' => array(
            'destroy'  => true,
        ),
        'authContainers' => array(
            'DB_Local' => array(
                'type' => $storage,
                'expireTime'    => 3600,
                'idleTime'      => 1800,
                'storage' => array(
                    'dbc' => $db,
                    'dsn' => $dsn,
                    'prefix' => 'liveuser_',
                    'tables' => array(
                        'users' => array(
                            'fields' => array(
                                'name' => false,
                                'email' => false,
                            ),
                        ),
                    ),
                    'fields' => array(
                        'name' => 'text',
                        'email' => 'text',
                    ),
                    'alias' => array(
                        'name' => 'name',
                        'email' => 'email',
                        'auth_user_id' => 'user_id',
                    ),
                    // 'force_seq' => false
                ),
            )
        ),
        'permContainer' => array(
            'type'  => 'Complex',
            'alias' => array(),
            'storage' => array(
                $storage => array(
                    'dbc' => $db,
                    'dsn' => $dsn,
                    'prefix' => 'liveuser_',
                    'tables' => array(),
                    'fields' => array(),
                    'alias' => array(),
                    // 'force_seq' => false
                ),
            ),
        ),
    );

$admin = LiveUser_Admin::factory($conf);
$logconf = array('mode' => 0666, 'timeFormat' => '%X %x');
$logger = Log::factory('file', 'liveuser_test.log', 'ident', $logconf);
$admin->log->addChild($logger);
$admin->init();
