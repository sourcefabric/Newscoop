<?php
/**
 * This script is fix for "The file adodb.inc.php was accessible only by root."
  */
$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$GLOBALS['g_campsiteDir'] = $cs_dir;
require_once(dirname(__FILE__) . "/../../../../conf/install_conf.php");
if (!is_array($Campsite)) {
	echo "Invalid configuration file(s)";
	exit(1);
}
$Campsite['ADODB_PATH'] = '/usr/share/php/adodb/';
$apacheUser = $Campsite['APACHE_USER'];
$apacheGroup = $Campsite['APACHE_GROUP'];
$adodbPath = $Campsite['ADODB_PATH'];
chown($adodbPath, $apacheUser);