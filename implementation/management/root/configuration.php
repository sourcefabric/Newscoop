<?php

global $Campsite, $ADMIN_DIR, $ADMIN;

$configuration_files = array("install_conf.php", "database_conf.php", "apache_conf.php", "parser_conf.php", "smtp_conf.php");

foreach ($configuration_files as $index=>$conf_file)
	require($_SERVER['DOCUMENT_ROOT'] . "/$conf_file");

$Campsite['HTML_COMMON_DIR'] = $Campsite['WWW_COMMON_DIR'] . "/html";
$Campsite['CGI_COMMON_DIR'] = $Campsite['WWW_COMMON_DIR'] . "/cgi-bin";
$Campsite['INCLUDE_COMMON_DIR'] = $Campsite['WWW_COMMON_DIR'] . "/include";

$db_name = $Campsite['DATABASE_NAME'];
$Campsite['HTML_DIR'] = $Campsite['WWW_DIR'] . "/$db_name/html";
$Campsite['CGI_DIR'] = $Campsite['WWW_DIR'] . "/$db_name/cgi-bin";
$Campsite['INCLUDE_DIR'] = $Campsite['WWW_DIR'] . "/$db_name/include";

$ADMIN_DIR = "admin-files";
$ADMIN = "admin";

?>