<?php

require_once("$DOCUMENT_ROOT/include/adodb/adodb.inc.php");

global $Campsite;
$Campsite = array();

$Campsite["db"] = ADONewConnection("mysql"); # eg 'mysql' or 'postgres'
//$Campsite["db"]->debug = true;
// Set fetch mode to return associative arrays
$Campsite["db"]->SetFetchMode(ADODB_FETCH_ASSOC);
$Campsite["db"]->Connect("localhost", "root", "", "campsite");

?>