<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

global $Campsite, $ADMIN;

$Campsite['APACHE_USER'] = 'www-data';
$Campsite['APACHE_GROUP'] = 'www-data';
$Campsite['CAMPSITE_DIR'] = (strlen($GLOBALS['g_campsiteDir']) > 0)
    ? $GLOBALS['g_campsiteDir'] : dirname(dirname(__FILE__));
$Campsite['ETC_DIR'] = $Campsite['CAMPSITE_DIR'].'/conf';
$Campsite['BIN_DIR'] = $Campsite['CAMPSITE_DIR'].'/bin';
$Campsite['WWW_DIR'] = $Campsite['CAMPSITE_DIR'];

$Campsite['HTML_DIR'] = $Campsite['CAMPSITE_DIR'];

$Campsite['SSL_SITE'] = FALSE;
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) {
    $scheme = 'https://';
    $Campsite['SSL_SITE'] = TRUE;
} elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
    $scheme = 'https://';
    $Campsite['SSL_SITE'] = TRUE;
} else {
    $scheme = 'http://';
}

if (!isset($_SERVER['SERVER_PORT'])) {
    $_SERVER['SERVER_PORT'] = 80;
}
$Campsite['HOSTNAME'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "";
if (($_SERVER['SERVER_PORT'] != 80) && ($_SERVER['SERVER_PORT'] != 443)) {
    $Campsite['HOSTNAME'] .= ':'.$_SERVER['SERVER_PORT'];
}
$Campsite['SUBDIR'] = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/', -2));
$ADMIN = empty($Campsite['SUBDIR']) ? 'admin' : substr($Campsite['SUBDIR'], 1) . '/admin';
$Campsite['WEBSITE_URL'] = $scheme.$Campsite['HOSTNAME'].$Campsite['SUBDIR'];
unset($scheme);


$Campsite['TEMPLATE_DIRECTORY'] = $Campsite['HTML_DIR']."/templates";
$Campsite['TEMPLATE_BASE_URL'] = $Campsite['WEBSITE_URL']."/templates";
$Campsite['IMAGE_DIRECTORY'] = $Campsite['HTML_DIR'].'/images/';
$Campsite['IMAGE_BASE_URL'] = $Campsite['WEBSITE_URL'].'/images/';
$Campsite['ADMIN_STYLE_URL'] = $Campsite['WEBSITE_URL'].'/admin-style';
$Campsite['ADMIN_IMAGE_BASE_URL'] = $Campsite['WEBSITE_URL'].'/admin-style/images';
$Campsite['IMAGE_PREFIX'] = 'cms-image-';
$Campsite['IMAGEMAGICK_INSTALLED'] = true;
$Campsite['THUMBNAIL_MAX_SIZE'] = 64;
$Campsite['THUMBNAIL_COMMAND'] = 'convert -sample '
    .$Campsite['THUMBNAIL_MAX_SIZE'].'x'.$Campsite['THUMBNAIL_MAX_SIZE'];
$Campsite['THUMBNAIL_DIRECTORY'] = $Campsite['IMAGE_DIRECTORY'].'/thumbnails/';
$Campsite['THUMBNAIL_BASE_URL'] = $Campsite['WEBSITE_URL'].'/images/thumbnails/';
$Campsite['THUMBNAIL_PREFIX'] = 'cms-thumb-';
$Campsite['FILE_BASE_URL'] = $Campsite['WEBSITE_URL'].'/files/';
$Campsite['FILE_DIRECTORY'] = $Campsite['HTML_DIR'].'/files';
$Campsite['FILE_NUM_DIRS_LEVEL_1'] = "1000";
$Campsite['FILE_NUM_DIRS_LEVEL_2'] = "1000";
$Campsite['TMP_DIRECTORY'] = '/tmp/';
$Campsite['HELP_URL'] = 'http://www.sourcefabric.org/en/products/newscoop_support/';
$Campsite['ABOUT_URL'] = 'http://www.sourcefabric.org/en/about/page/';
$Campsite['SUPPORT_EMAIL'] = 'newscoop-bug@sourcefabric.org';
$Campsite['DEBUG'] = true;

require_once($Campsite['CAMPSITE_DIR'].'/template_engine/classes/CampVersion.php');
$version = new CampVersion();
$Campsite['VERSION'] = $version->getVersion();
?>
