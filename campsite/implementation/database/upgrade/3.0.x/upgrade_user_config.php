<?php

require_once("database_conf.php");
require_once("install_conf.php");
if (!is_array($Campsite)) {
	echo "Invalid configuration file(s)";
	exit(1);
}

$db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];
$max_upload_filesize = ini_get('upload_max_filesize');

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
	die("Unable to connect to the database.\n");
}

if (!mysql_select_db($db_name)) {
	die("Unable to use the database " . $db_name . ".\n");
}

// 
// populate the UserConfig table
//
$sql = "INSERT INTO UserConfig (fk_user_id, varname, value, last_modified) VALUES ('0','MaxUploadFileSize','".$max_upload_filesize."',NOW())";
if (!($res = mysql_query($sql))) {
	die("Unable to write to the database.\n");
}

?>
