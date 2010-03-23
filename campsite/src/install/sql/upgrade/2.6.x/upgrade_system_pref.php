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
$max_upload_filesize = ini_get('upload_max_filesize');

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
    die("Unable to connect to the database.\n");
}

if (!mysql_select_db($db_name)) {
    die("Unable to use the database " . $db_name . ".\n");
}
mysql_query("SET NAMES 'utf8'");

//
// get system preferences options from UserConfig
//
$sql = "SELECT varname, value FROM UserConfig WHERE fk_user_id = 0";
if (!($res = mysql_query($sql))) {
	die("Unable to read from the database.\n");
}
//
// copy system preferences from UserConfig to SystemPreferences
//
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	$sql = "INSERT INTO SystemPreferences (varname, value)
			VALUES ('".mysql_real_escape_string($row['varname'])."', '"
					 .mysql_real_escape_string($row['value'])."')
			ON DUPLICATE KEY UPDATE value = '".mysql_real_escape_string($row['value'])."'";
	mysql_query($sql);
}

//
// deletes system preferences options from UserConfig
//
$sql = "DELETE FROM UserConfig WHERE fk_user_id = 0";
if (!($res = mysql_query($sql))) {
	die("Unable to write to the database.\n");
}

//
// populate the SystemPreferences table
//
$sql = "INSERT INTO SystemPreferences (varname, value)
        VALUES ('MaxUploadFileSize','".$max_upload_filesize."'),
               ('UseDBReplication','N'),
               ('DBReplicationHost',''),
               ('DBReplicationUser',''),
               ('DBReplicationPass',''),
               ('DBReplicationPort','3306'),
               ('UseCampcasterAudioclips','N'),
               ('CampcasterHostName','localhost'),
               ('CampcasterHostPort','80'),
               ('CampcasterXRPCPath','/campcaster/storageServer/var/xmlrpc/'),
               ('CampcasterXRPCFile','xrLocStor.php')";
if (!($res = mysql_query($sql))) {
    die("Unable to write to the database.\n");
}

?>
