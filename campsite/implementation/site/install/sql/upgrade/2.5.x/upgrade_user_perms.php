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
$templates_dir = $Campsite['WWW_DIR'] . "/$db_name/html/look";

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
	die("Unable to connect to the database.\n");
}

if (!mysql_select_db($db_name)) {
	die("Unable to use the database " . $db_name . ".\n");
}
mysql_query("SET NAMES 'utf8'");


//
// Add new permissions for each user.
//
if (!($res = mysql_query("SELECT DISTINCT(fk_user_id) FROM UserConfig WHERE fk_user_id!=0"))) {
	die("Unable to read from the database.\n");
}
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	// Set the new "CommentModerate" permission to have the same value as the
	// "Publish" permission.
	$sql = "SELECT value FROM UserConfig WHERE fk_user_id=".$row['fk_user_id']." AND varname='Publish'";
	$result = mysql_query($sql);
	$row2 = mysql_fetch_array($result, MYSQL_ASSOC);

	// Add the "CommentModerate" permission.
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES (".$row['fk_user_id'].", 'CommentModerate', '".$row2['value']."')";
	mysql_query($sql);

	// Set the new "CommentEnable" permission to have the same value as the
	// "Publish" permission.
	$sql = "SELECT value FROM UserConfig WHERE fk_user_id=".$row['fk_user_id']." AND varname='Publish'";
	$result = mysql_query($sql);
	$row2 = mysql_fetch_array($result, MYSQL_ASSOC);

	// Add the "CommentEnable" permission.
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES (".$row['fk_user_id'].", 'CommentEnable', '".$row2['value']."')";
	mysql_query($sql);

}


//
// Add new permissions to the UserType table.
//
if (!($res = mysql_query("SELECT DISTINCT(user_type_name) FROM UserTypes"))) {
	die("Unable to read from the database.\n");
}
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	$userTypeName = $row['user_type_name'];

	// Add new permissions

	// Set the new "CommentModerate" permission to have the same value as the
	// "Publish" permission.
	$sql = "SELECT value FROM UserTypes WHERE user_type_name='$userTypeName' AND varname='Publish'";
	$result = mysql_query($sql);
	$row2 = mysql_fetch_array($result, MYSQL_ASSOC);

	// Add the "CommentModerate" permission.
	$sql = "INSERT INTO UserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'CommentModerate', '".$row2['value']."')";
	mysql_query($sql);

	// Set the new "CommentEnable" permission to have the same value as the
	// "Publish" permission.
	$sql = "SELECT value FROM UserTypes WHERE user_type_name='$userTypeName' AND varname='Publish'";
	$result = mysql_query($sql);
	$row2 = mysql_fetch_array($result, MYSQL_ASSOC);

	// Add the "CommentEnable" permission.
	$sql = "INSERT INTO UserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'CommentEnable', '".$row2['value']."')";
	mysql_query($sql);
}

?>