<?php

global $Campsite, $ADMIN_DIR, $ADMIN, $g_documentRoot;

// We indirectly reference the document root because some 
// scripts that use this file run from the command line,
// therefore $_SERVER['DOCUMENT_ROOT'] is not defined in 
// these cases.
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}

$configuration_files = array("install_conf.php", 
							 "database_conf.php", 
							 "apache_conf.php", 
							 "parser_conf.php", 
							 "smtp_conf.php");

foreach ($configuration_files as $index=>$conf_file) {
	require($g_documentRoot . "/$conf_file");
}
unset($configuration_files);

$Campsite['HTML_COMMON_DIR'] = $Campsite['WWW_COMMON_DIR'] . "/html";
$Campsite['CGI_COMMON_DIR'] = $Campsite['WWW_COMMON_DIR'] . "/cgi-bin";
$Campsite['HTML_DIR'] = $Campsite['WWW_DIR'].'/'.$Campsite['DATABASE_NAME'].'/html';
$Campsite['CGI_DIR'] = $Campsite['WWW_DIR'].'/'.$Campsite['DATABASE_NAME'].'/cgi-bin';

$ADMIN_DIR = "admin-files";
$ADMIN = "admin";

if (!isset($_SERVER['SERVER_PORT']))
{
	$_SERVER['SERVER_PORT'] = 80;
}
$scheme = $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
$Campsite['HOSTNAME'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "";
if (($_SERVER['SERVER_PORT'] != 80) && ($_SERVER['SERVER_PORT'] != 443)) {
    $Campsite['HOSTNAME'] .= ':'.$_SERVER['SERVER_PORT'];
}
$Campsite['WEBSITE_URL'] = $scheme.$Campsite['HOSTNAME'];
unset($scheme);

require_once($g_documentRoot.'/campsite_version.php');

$Campsite['IMAGE_DIRECTORY'] = $Campsite['HTML_DIR'].'/images/';
$Campsite['IMAGE_BASE_URL'] = $Campsite['WEBSITE_URL'].'/images/';
$Campsite['IMAGE_PREFIX'] = 'cms-image-';
$Campsite['IMAGEMAGICK_INSTALLED'] = true;
$Campsite['THUMBNAIL_COMMAND'] = 'convert -sample 64x64';
$Campsite['THUMBNAIL_DIRECTORY'] = $Campsite['IMAGE_DIRECTORY'].'/thumbnails/';
$Campsite['THUMBNAIL_BASE_URL'] = $Campsite['WEBSITE_URL'].'/images/thumbnails/';
$Campsite['THUMBNAIL_PREFIX'] = 'cms-thumb-';
$Campsite['TMP_DIRECTORY'] = '/tmp/';
$Campsite['HELP_URL'] = 'http://code.campware.org/manuals/campsite/2.3/';
$Campsite['ABOUT_URL'] = 'http://www.campware.org/en/camp/campsite_news/';

?>