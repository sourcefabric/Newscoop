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

if (!($res = mysql_query("SELECT DISTINCT(fk_user_id) FROM UserConfig WHERE fk_user_id!=0"))) {
	die("Unable to read from the database.\n");
}
$indexExists = false;
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	if ($row['Key_name'] == 'IdImage') {
		$indexExists = true;
		break;
	}
}

if (!$indexExists) {
	mysql_query('ALTER TABLE `ArticleImages` ADD INDEX `IdImage`(`IdImage`)');
}

?>