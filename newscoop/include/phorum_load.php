<?php
global $Campsite;
global $PHORUM;
define( "PHORUM", "5.1-dev" );

require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
require_once($GLOBALS['g_campsiteDir'].'/include/phorum/include/constants.php');

$dbName = $Campsite['DATABASE_NAME'];
if (SystemPref::Get("UseDBReplication") == 'Y') {
    $dbHost = SystemPref::Get('DBReplicationHost')
            . ':'
            . SystemPref::Get('DBReplicationPort');
    $dbUser = SystemPref::Get('DBReplicationUser');
    $dbPass = SystemPref::Get('DBReplicationPass');
} else {
    $dbHost = $Campsite['DATABASE_SERVER_ADDRESS'];
    $dbUser = $Campsite['DATABASE_USER'];
    $dbPass = $Campsite['DATABASE_PASSWORD'];
}

$PHORUM['DBCONFIG']=array(
    // Database connection.
    'type'          =>  'mysql',
    'name'          =>  $dbName,
	'server'		=>  $dbHost,
	'user'			=>  $dbUser,
	'password'		=>  $dbPass,
    'table_prefix'  =>  'phorum',

    // 'down_page'     => 'http://www.example.com/phorum/down.html',

    // 1=enabled, 0=disabled
    // (always disable this option for MySQL versions prior to 4.0.18!)
    'mysql_use_ft'  =>  '1'
);

require_once($GLOBALS['g_campsiteDir']."/include/phorum/include/db/mysql.php");

?>