<?php

require_once 'DB.php';
require_once 'adodb/adodb.inc.php';

require_once dirname(__FILE__) . '/conf/database_conf.php';

global $Campsite;
global $g_ado_db, $db, $dsn;

if (!isset($g_ado_db)) {
	// Data Source Name (DSN)
    $dsn = sprintf('mysql://%s:%s@%s:%d/%s',
        $Campsite['db']['user'],
        $Campsite['db']['pass'],
        $Campsite['db']['host'],
        (int) $Campsite['db']['port'],
        $Campsite['db']['name']);

    $db = DB::connect($dsn);
	if (PEAR::isError($db)) {
        display_error();
	}

	$g_ado_db = ADONewConnection('mysql'); # eg 'mysql' or 'postgres'

	// Set fetch mode to return associative arrays
	$g_ado_db->SetFetchMode(ADODB_FETCH_ASSOC);

	$g_ado_db->_connectionID = $db->connection;
    $g_ado_db->Execute("SET NAMES 'utf8'");
}

if (!$g_ado_db->IsConnected()) {
    display_error();
}

/**
 * Display error message and die.
 *
 * @return void
 */
function display_error()
{
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
?>
<div style="color:red;font-size:2em">
    <p>ERROR connecting to the MySQL server!</p>
    <p>Please start the MySQL database server and verify if the connection
configuration is valid.</p>
</div>
<?php
    exit(1);
}
?>
