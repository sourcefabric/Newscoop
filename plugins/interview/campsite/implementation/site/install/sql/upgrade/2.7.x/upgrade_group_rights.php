<?php

$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once("$cs_dir/conf/database_conf.php");
require_once("$cs_dir/conf/install_conf.php");
if (!is_array($Campsite)) {
    echo "Invalid configuration file(s)";
    exit(1);
}

$db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
    die("Unable to connect to the database.\n");
}

if (!mysql_select_db($db_name)) {
    die("Unable to use the database " . $db_name . ".\n");
}

//
// get system preferences options from UserConfig
//
$sql = "SELECT right_id FROM liveuser_rights WHERE right_define_name = 'ClearCache'";
if (!($res = mysql_query($sql))) {
	die("Unable to read from the database.\n");
}

//
// add "ClearCache" right to the "admin" group
//
$row = mysql_fetch_array($res, MYSQL_ASSOC);
if (!$row) {
    die("Unable to read from the database.\n");
}
$sql = "INSERT INTO liveuser_grouprights (group_id, right_id, right_level)
		VALUES (1, ".$row['right_id'].", 3)";
if (!($res = mysql_query($sql))) {
    die("Unable to write to the database.\n");
}

?>
