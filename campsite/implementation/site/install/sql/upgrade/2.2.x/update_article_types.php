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

if (!mysql_connect($db_host, $db_user, $db_passwd))
	die("Unable to connect to the database.\n");
if (!mysql_select_db($db_name))
	die("Unable to use the database " . $db_name . ".\n");

if (!($res = mysql_query("SHOW TABLES LIKE 'X%'")))
	die("Unable to read from the database.\n");
while ($row = mysql_fetch_row($res)) {
	$tableName = $row[0];
	if (!($tableRes = mysql_query("DESC `$tableName` 'F%'")))
		die("Unable to read from the database.\n");
	while ($tableRow = mysql_fetch_row($tableRes)) {
		$fieldName = $tableRow[0];
		$fieldType = $tableRow[1];
		if (strpos(strtolower($fieldType), 'varchar') !== false
			|| strpos(strtolower($fieldType), 'varbinary') !== false) {
			$queryStr = "ALTER TABLE `$tableName` MODIFY COLUMN `$fieldName` "
				. "VARCHAR(255) NOT NULL DEFAULT ''";
			mysql_query($queryStr);
		}
	}
}

?>
