<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/include/adodb/adodb.inc.php');

global $Campsite;
$Campsite = array();

$Campsite['db'] = ADONewConnection('mysql'); # eg 'mysql' or 'postgres'
//$Campsite['db']->debug = true;
// Set fetch mode to return associative arrays
$Campsite['db']->SetFetchMode(ADODB_FETCH_ASSOC);
$Campsite['db']->Connect('localhost', 'root', '', 'campsite');

$scheme = (substr($_SERVER['REQUEST_URI'], 0, 5) == 'https')?'https://':'http://';
$Campsite['website_url'] = $scheme.$_SERVER['SERVER_NAME'];

$Campsite['version'] = '2.2';
?>