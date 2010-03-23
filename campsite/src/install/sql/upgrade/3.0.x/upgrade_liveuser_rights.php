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
mysql_query("SET NAMES 'utf8'");

//
// removes InitializeTemplateEngine right from users and groups except for the admin group
//
$sql1 = 'DELETE FROM liveuser_grouprights WHERE right_id = 51 AND group_id <> 1';
$sql2 = 'DELETE FROM liveuser_userrights WHERE right_id = 51';
if (!(mysql_query($sql1)) || !(mysql_query($sql2))) {
    die("Unable to write to the database.\n");
}

//
// changes the right name from InitializeTemplateEngine to plugin_manager
//
$sql = "UPDATE liveuser_rights SET right_define_name = 'plugin_manager' WHERE right_id = 51";
if (!(mysql_query($sql))) {
    die("Unable to write to the database.\n");
}

?>
