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

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
        die("Unable to connect to the database.\n");
}

if (!mysql_select_db($db_name)) {
        die("Unable to use the database " . $db_name . ".\n");
}

// Get all the campsite users (subscribers)
if (!($res = mysql_query("SELECT Id, UName, Password, EMail FROM Users ORDER BY Id"))) {
        die("Unable to read from the database.\n");
}

while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	// find out if the campsite user has already a corresponding phorum user 
	$result = mysql_query("SELECT user_id FROM phorum_users WHERE fk_campsite_user_id = '" . $row['Id'] . "'");
	$user = mysql_fetch_array($result, MYSQL_ASSOC);
	if (!$user) {
		// insert the corresponding phorum user
		$sql = "INSERT INTO phorum_users (fk_campsite_user_id, username, password, email) "
				. "VALUES (" . $row['Id'] . ",'" . $row['UName'] . "','" . $row['Password'] . "','" . $row['EMail'] ."')";
		mysql_query($sql);
	}

}

?>
