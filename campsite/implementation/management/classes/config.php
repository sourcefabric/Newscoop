<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/include/adodb/adodb.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');

global $Campsite;
$Campsite = array();

$Campsite['db'] = ADONewConnection('mysql'); # eg 'mysql' or 'postgres'
//$Campsite['db']->debug = true;
// Set fetch mode to return associative arrays
$Campsite['db']->SetFetchMode(ADODB_FETCH_ASSOC);
$Campsite['db']->Connect($Campsite['DATABASE_SERVER_ADDRESS'], $Campsite['DATABASE_USER'],
	$Campsite['DATABASE_PASSWORD'], $Campsite['DATABASE_NAME']);

$scheme = (substr($_SERVER['REQUEST_URI'], 0, 5) == 'https')?'https://':'http://';

$Campsite['hostname'] = $_SERVER['SERVER_NAME'];
$Campsite['website_url'] = $scheme.$Campsite['hostname'];

$Campsite['version'] = '2.2';

?>
