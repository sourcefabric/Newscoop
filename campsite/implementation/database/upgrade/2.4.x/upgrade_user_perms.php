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


//
// Add new permissions for each user.
//
if (!($res = mysql_query("SELECT DISTINCT(fk_user_id) FROM UserConfig"))) {
	die("Unable to read from the database.\n");
}
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	// Set the new "MoveArticle" permission to have the same value as the 
	// "ChangeArticle" permission.
	$sql = "SELECT value FROM UserConfig WHERE fk_user_id=".$row['fk_user_id']." AND varname='ChangeArticle'";
	$result = mysql_query($sql);
	$row2 = mysql_fetch_array($result, MYSQL_ASSOC);
	
	// Add the "MoveArticle" permission.
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES (".$row['fk_user_id'].", 'MoveArticle', '".$row2['value']."')";
	mysql_query($sql);

	// Set the new "TranslateArticle" permission to have the same value as the 
	// "AddArticle" permission.
	$sql = "SELECT value FROM UserConfig WHERE fk_user_id=".$row['fk_user_id']." AND varname='AddArticle'";
	$result = mysql_query($sql);
	$row2 = mysql_fetch_array($result, MYSQL_ASSOC);
	
	// Add the "TranslateArticle" permission.
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES (".$row['fk_user_id'].", 'TranslateArticle', '".$row2['value']."')";
	mysql_query($sql);
	
	// Set the new "AttachImageToArticle" permission to have the same value as the 
	// "AddImage" permission.
	$sql = "SELECT value FROM UserConfig WHERE fk_user_id=".$row['fk_user_id']." AND varname='AddImage'";
	$result = mysql_query($sql);
	$row2 = mysql_fetch_array($result, MYSQL_ASSOC);
	
	// Add the "AttachImageToArticle" permission.
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES (".$row['fk_user_id'].", 'AttachImageToArticle', '".$row2['value']."')";
	mysql_query($sql);
	
	// Add the "AttachTopicToArticle" permission.
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES (".$row['fk_user_id'].", 'AttachTopicToArticle', 'Y')";
	mysql_query($sql);
	
	// Add the "EditorFindReplace" permission.
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES (".$row['fk_user_id'].", 'EditorFindReplace', 'Y')";
	mysql_query($sql);

	// Add the "EditorCharacterMap" permission.
	$sql = "INSERT INTO UserConfig(fk_user_id, varname, value) "
			." VALUES (".$row['fk_user_id'].", 'EditorCharacterMap', 'Y')";
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

	// Set the new "MoveArticle" permission to have the same value as the 
	// "ChangeArticle" permission.
	$sql = "SELECT value FROM UserTypes WHERE user_type_name='$userTypeName' AND varname='ChangeArticle'";
	$result = mysql_query($sql);
	$row2 = mysql_fetch_array($result, MYSQL_ASSOC);

	// Add the "MoveArticle" permission.
	$sql = "INSERT INTO UserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'MoveArticle', '".$row2['value']."')";
	mysql_query($sql);
	
	// Set the new "TranslateArticle" permission to have the same value as the 
	// "AddArticle" permission.
	$sql = "SELECT value FROM UserTypes WHERE user_type_name='$userTypeName' AND varname='AddArticle'";
	$result = mysql_query($sql);
	$row2 = mysql_fetch_array($result, MYSQL_ASSOC);

	// Add the "TranslateArticle" permission.
	$sql = "INSERT INTO UserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'TranslateArticle', '".$row2['value']."')";
	mysql_query($sql);
	
	// Set the new "AttachImageToArticle" permission to have the same value as the 
	// "AddImage" permission.
	$sql = "SELECT value FROM UserTypes WHERE user_type_name='$userTypeName' AND varname='AddImage'";
	$result = mysql_query($sql);
	$row2 = mysql_fetch_array($result, MYSQL_ASSOC);

	// Add the "AttachImageToArticle" permission.
	$sql = "INSERT INTO UserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'AttachImageToArticle', '".$row2['value']."')";
	mysql_query($sql);
	
	// Add the "AttachTopicToArticle" permission.
	$sql = "INSERT INTO UserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'AttachTopicToArticle', 'Y')";
	mysql_query($sql);
	
	// Add the "EditorFindReplace" permission.
	$sql = "INSERT INTO UserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'EditorFindReplace', 'Y')";
	mysql_query($sql);

	// Add the "EditorCharacterMap" permission.
	$sql = "INSERT INTO UserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'EditorCharacterMap', 'Y')";
	mysql_query($sql);
	
}

?>