<?php

$GLOBALS['g_campsiteDir'] = dirname(__FILE__);
require_once('adodb/adodb.inc.php');
require_once($GLOBALS['g_campsiteDir'].'/conf/database_conf.php');
require_once('DB.php');

global $g_ado_db, $db, $dsn;
global $Campsite;

if (!isset($g_ado_db)) {
	// Data Source Name (DSN)
	$dsn = 'mysql://'.$Campsite['db']['user']
	.':'.$Campsite['db']['pass']
    .'@'.$Campsite['db']['host']
    .':'.$Campsite['db']['port']
	.'/'.$Campsite['db']['name'];

	$db = DB::connect($dsn);
	if (PEAR::isError($db)) {
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		?>
<font color="red" size="3">
<p>ERROR connecting to the MySQL server!</p>
<p>Please start the MySQL database server and verify if the connection
configuration is valid.</p>
</font>
		<?php
		exit(0);
	}

	$g_ado_db = ADONewConnection($Campsite['db']['type']); # eg 'mysql' or 'postgres'
	// Set fetch mode to return associative arrays
	$g_ado_db->SetFetchMode(ADODB_FETCH_ASSOC);

	$g_ado_db->_connectionID = $db->connection;
    $g_ado_db->Execute("SET NAMES 'utf8'");
}

if (!$g_ado_db->IsConnected()) {
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
?>
	<font color="red" size="3">
	<p>ERROR connecting to the MySQL server!</p>
	<p>Please start the MySQL database server and verify if the connection configuration is valid.</p>
	</font>
<?php
	exit(0);
}

?>
