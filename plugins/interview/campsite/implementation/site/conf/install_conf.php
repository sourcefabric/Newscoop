<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

$Campsite['APACHE_USER'] = 'www-data';
$Campsite['APACHE_GROUP'] = 'www-data';
$Campsite['CAMPSITE_DIR'] = $_SERVER['DOCUMENT_ROOT'];
$Campsite['BIN_DIR'] = $Campsite['CAMPSITE_DIR'].'/bin';
$Campsite['WWW_DIR'] = $Campsite['CAMPSITE_DIR'];

$Campsite['HTML_DIR'] = $Campsite['CAMPSITE_DIR'];

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


$Campsite['TEMPLATE_DIRECTORY'] = $Campsite['HTML_DIR']."/templates";
$Campsite['TEMPLATE_BASE_URL'] = $Campsite['WEBSITE_URL']."/templates/";
$Campsite['IMAGE_DIRECTORY'] = $Campsite['HTML_DIR'].'/images/';
$Campsite['IMAGE_BASE_URL'] = $Campsite['WEBSITE_URL'].'/images/';
$Campsite['ADMIN_IMAGE_BASE_URL'] = $Campsite['WEBSITE_URL'].'/css';
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
$Campsite['HELP_URL'] = 'http://code.campware.org/manuals/campsite/3.0/';
$Campsite['ABOUT_URL'] = 'http://www.campware.org/en/camp/campsite_news/';
$Campsite['SUPPORT_EMAIL'] = 'campsite-support@lists.campware.org';
$Campsite['DEBUG'] = true;
$Campsite['VERSION'] = '3.0-beta3';

?>