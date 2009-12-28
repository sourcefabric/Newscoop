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

if (!($res = mysql_query("SELECT * FROM UserPerm"))) {
	die("Unable to read from the database.\n");
}

while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	$userId = $row['IdUser'];
	foreach ($row as $columnName => $value) {
		// Only add permissions that we still use.
		if (($columnName != "IdUser") 
			&& ($columnName != "Reader")
			&& ($columnName != 'DeleteDictionary')
			&& ($columnName != 'ManageClasses')
			&& ($columnName != 'ManageDictionary')) {
			$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) VALUES ($userId, '$columnName', '$value')";
			mysql_query($sql);
		}
	} // foreach
	
	// Add new permissions
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES ($userId, 'AddFile', '".$row['AddImage']."')";
	mysql_query($sql);
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES ($userId, 'ChangeFile', '".$row['ChangeImage']."')";
	mysql_query($sql);
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES ($userId, 'DeleteFile', '".$row['DeleteImage']."')";
	mysql_query($sql);
}

if (!($res = mysql_query("SELECT * FROM UserTypes WHERE Name!='Reader'"))) {
	die("Unable to read from the database.\n");
}

while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	$userTypeName = $row['Name'];
	foreach ($row as $columnName => $value) {
		// Only add permissions that we still use.
		if (($columnName != "Name") 
			&& ($columnName != "Reader")
			&& ($columnName != 'DeleteDictionary')
			&& ($columnName != 'ManageClasses')
			&& ($columnName != 'ManageDictionary')) {
			$sql = "INSERT INTO TmpUserTypes(user_type_name, varname, value) VALUES ('$userTypeName', '$columnName', '$value')";
			mysql_query($sql);
		}
	} // foreach

	// Add new permissions
	$sql = "INSERT INTO TmpUserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'AddFile', '".$row['AddImage']."')";
	mysql_query($sql);
	$sql = "INSERT INTO TmpUserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'ChangeFile', '".$row['ChangeImage']."')";
	mysql_query($sql);
	$sql = "INSERT INTO TmpUserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'DeleteFile', '".$row['DeleteImage']."')";
	mysql_query($sql);
}

?>
