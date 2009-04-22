<?php

// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/include/adodb/adodb.inc.php');
require_once($g_documentRoot.'/conf/database_conf.php');

global $g_ado_db;
global $Campsite;

if (!isset($g_ado_db)) {
	$g_ado_db = ADONewConnection('mysql'); # eg 'mysql' or 'postgres'
	//$g_ado_db->debug = true;
	// Set fetch mode to return associative arrays
	$g_ado_db->SetFetchMode(ADODB_FETCH_ASSOC);
	$g_ado_db->Connect($Campsite['DATABASE_SERVER_ADDRESS'], $Campsite['DATABASE_USER'],
		$Campsite['DATABASE_PASSWORD'], $Campsite['DATABASE_NAME']);
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