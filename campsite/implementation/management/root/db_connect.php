<?php

// We indirectly reference the DOCUMENT_ROOT so we can enable 
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT'] 
// is not defined in these cases.
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/include/adodb/adodb.inc.php');
require_once($g_documentRoot.'/configuration.php');

global $Campsite;
if (!isset($Campsite['db'])) {
	$Campsite['db'] = ADONewConnection('mysql'); # eg 'mysql' or 'postgres'
	//$Campsite['db']->debug = true;
	// Set fetch mode to return associative arrays
	$Campsite['db']->SetFetchMode(ADODB_FETCH_ASSOC);
	$Campsite['db']->Connect($Campsite['DATABASE_SERVER_ADDRESS'], $Campsite['DATABASE_USER'],
		$Campsite['DATABASE_PASSWORD'], $Campsite['DATABASE_NAME']);
}

?>