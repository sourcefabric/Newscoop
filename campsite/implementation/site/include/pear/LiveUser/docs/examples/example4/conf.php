<?php

define('EMAIL_WEBMASTER', 'krausbn@php.net');

// PEAR path
//$path_to_liveuser_dir = 'path/to/pear/'.PATH_SEPARATOR;
//ini_set('include_path', $path_to_liveuser_dir . ini_get('include_path'));

error_reporting(E_ALL);

require_once 'PEAR.php';
include_once 'HTML/Template/IT.php';
require_once 'MDB2.php';

function php_error_handler($errno, $errstr, $errfile, $errline)
{
    if (error_reporting() && $errno != 2048) {
        $tpl = new HTML_Template_IT();
        $tpl->loadTemplatefile('error-page.tpl.php');

        $tpl->setVariable('error_msg', "<b>$errfile ($errline)</b><br />$errstr");
        $tpl->show();
    }
}

set_error_handler('php_error_handler');

function pear_error_handler($err_obj)
{
    $error_string = $err_obj->getMessage() . '<br />' . $err_obj->getUserInfo();
    trigger_error($error_string, E_USER_ERROR);
}

PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'pear_error_handler');

// Data Source Name (DSN)
//$dsn = '{dbtype}://{user}:{passwd}@{dbhost}/{dbname}';
$dsn = 'mysql://root:@localhost/liveuser_test_example4';

$db = MDB2::connect($dsn, true);
$db->setFetchMode(MDB2_FETCHMODE_ASSOC);


$tpl = new HTML_Template_IT();

$LUOptions = array(
    'login' => array(
        'force'    => true
     ),
    'logout' => array(
        'destroy'  => true,
     ),
    'authContainers' => array(
        array(
            'type'         => 'MDB2',
            'expireTime'   => 3600,
            'idleTime'     => 1800,
            'storage' => array(
                'dsn' => $dsn,
                'alias' => array(
                    'auth_user_id' => 'authUserId',
                    'lastlogin' => 'lastLogin',
                    'is_active' => 'isActive',
                    'owner_user_id' => 'owner_user_id',
                    'owner_group_id' => 'owner_group_id',
                    'users' => 'peoples',
                ),
                'fields' => array(
                    'lastlogin' => 'timestamp',
                    'is_active' => 'boolean',
                    'owner_user_id' => 'integer',
                    'owner_group_id' => 'integer',
                ),
                'tables' => array(
                    'users' => array(
                        'fields' => array(
                            'lastlogin' => false,
                            'is_active' => false,
                            'owner_user_id' => false,
                            'owner_group_id' => false,
                        ),
                    ),
                ),
            ),
        ),
        array(
            'type' => 'XML',
            'expireTime'   => 3600,
            'idleTime'     => 1800,
            'passwordEncryptionMode' => 'MD5',
            'storage' => array(
                'file' => 'Auth_XML.xml',
                'alias' => array(
                    'auth_user_id' => 'userId',
                    'passwd' => 'password',
                    'lastlogin' => 'lastLogin',
                    'is_active' => 'isActive',
                ),
            ),
        ),
    ),
    'permContainer' => array(
        'type' => 'Complex',
        'storage' => array(
            'MDB2' => array(
                'dsn' => $dsn,
                'prefix' => 'liveuser_',
                'alias' => array(
                    'perm_users' => 'perm_peoples',
                ),
            )
         ),
    ),
);

require_once 'LiveUser.php';

function forceLogin(&$notification)
{
    $liveUserObj =& $notification->getNotificationObject();

    $username = (array_key_exists('username', $_REQUEST)) ? $_REQUEST['username'] : null;
    if($username) {
        $password = (array_key_exists('password', $_REQUEST)) ? $_REQUEST['password'] : null;
        $liveUserObj->login($username, $password);
    }
    if (!$liveUserObj->isLoggedIn()) {
        showLoginForm($liveUserObj);
    }
}

function showLoginForm(&$liveUserObj)
{
    $tpl = new HTML_Template_IT();
    $tpl->loadTemplatefile('loginform.tpl.php');

    $tpl->setVariable('form_action', $_SERVER['SCRIPT_NAME']);

    if (is_object($liveUserObj)) {
        if ($liveUserObj->getStatus()) {
            switch ($liveUserObj->getStatus()) {
                case LIVEUSER_STATUS_ISINACTIVE:
                    $tpl->touchBlock('inactive');
                    break;
                case LIVEUSER_STATUS_IDLED:
                    $tpl->touchBlock('idled');
                    break;
                case LIVEUSER_STATUS_EXPIRED:
                    $tpl->touchBlock('expired');
                    break;
                default:
                    $tpl->touchBlock('failure');
                    break;
            }
        }
    }

    $tpl->show();
    exit();
}

// Create new LiveUser (LiveUser) object.
// We�ll only use the auth container, permissions are not used.
$LU = LiveUser::factory($LUOptions);
$LU->dispatcher->addObserver('forceLogin', 'forceLogin');

if (!$LU->init()) {
    var_dump($LU->getErrors());
    die();
}

$logout = (array_key_exists('logout', $_REQUEST)) ? $_REQUEST['logout'] : false;

if ($logout) {
    $LU->logout(true);
    showLoginForm($LU);
}

define('AREA_NEWS',          1);
define('RIGHT_NEWS_NEW',     1);
define('RIGHT_NEWS_CHANGE',  2);
define('RIGHT_NEWS_DELETE',  3);