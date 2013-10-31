<?PHP
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
mysql_query("SET NAMES 'utf8'");

// Get all users
$sql = "SELECT * FROM Users WHERE Reader='N'";
$userResult = mysql_query($sql);
$users = array();
while ($user = mysql_fetch_assoc($userResult)) {
	$users[] = $user;
}

// Get all user types
$sql = "SELECT DISTINCT(user_type_name) as name FROM UserTypes";
$userTypeNamesResult = mysql_query($sql);

// Go through all the user types
while ($userTypeName = mysql_fetch_array($userTypeNamesResult)) {
	$userTypeName = $userTypeName[0];

	// Get all the permissions for the user type
	$config = array();
	$queryStr = 'SELECT varname, value FROM UserTypes '
				." WHERE user_type_name='".$userTypeName."'";
	$configResult = mysql_query($queryStr);
	while ($pair = mysql_fetch_assoc($configResult)) {
		$config[$pair['varname']] = $pair['value'];
	}

	//
	// Check if these permissions match the user.
	//

	// Create WHERE string check
	$where = array();
	foreach ($config as $name => $value) {
		if (is_bool($value)) {
			$value = $value ? 'Y' : 'N';
		}
		$where[] = "(varname='".mysql_real_escape_string($name)."'"
				  ." AND value='".mysql_real_escape_string($value)."')";
	}
	$whereStr = implode(' OR ', $where);
	$totalConfigValues = count($config);

	foreach ($users as $user) {
		$queryStr = "SELECT COUNT(*) as total FROM UserConfig "
					." WHERE fk_user_id=".$user['Id']
					." AND ($whereStr)";
		$numMatchesResult = mysql_query($queryStr);

		$numMatches = mysql_fetch_assoc($numMatchesResult);
		$numMatches = $numMatches['total'];

		if ($numMatches >= $totalConfigValues) {
			// Update the User to show his user type
			$sql = "UPDATE Users SET fk_user_type='".$userTypeName."'"
				   ." WHERE Id=".$user['Id'];
			mysql_query($sql);
		}
	} // foreach (all users)

} // while (all user types)
?>