<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

global $Campsite, $CampCfg;

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/database_conf.php');
require_once($g_documentRoot.'/smtp_conf.php');


/** System settings **/
$CampCfg['campsite']['url_default_type'] = 2;
$CampCfg['campsite']['secret_key'] = SystemPref::Get('SiteSecretKey');
$CampCfg['campsite']['session_lifetime'] = SystemPref::Get('SiteSessionLifeTime');

/** General settings **/
$CampCfg['site']['online'] = SystemPref::Get('SiteOnline');
$CampCfg['site']['title'] = SystemPref::Get('SiteTitle');
$CampCfg['site']['keywords'] = SystemPref::Get('SiteMetaKeywords');
$CampCfg['site']['description'] = SystemPref::Get('SiteMetaDescription');
$CampCfg['site']['charset'] = 'utf-8';
$CampCfg['site']['help_url'] = 'http://code.campware.org/manuals/campsite/2.6/';
$CampCfg['site']['about_url'] = 'http://www.campware.org/en/camp/campsite_news/';
$CampCfg['site']['email'] = 'campsite-support@lists.campware.org';

$CampCfg['campware']['url'] = 'http://www.campware.org/';
$CampCfg['campware']['mdlf_url'] = 'http://www.mdlf.org/';
$CampCfg['campware']['campcaster_url'] = 'http://www.campware.org/en/camp/campcaster_news/';

/** Locale settings **/
$CampCfg['locale']['lang_id'] = 1;
$CampCfg['locale']['lang_iso'] = 'en-US';
$CampCfg['locale']['lang_code'] = 'en';
$CampCfg['locale']['lang_name'] = 'english';

/** Database settings **/
$CampCfg['db']['type'] = 'mysql';
$CampCfg['db']['host'] = $Campsite['DATABASE_SERVER_ADDRESS'];
$CampCfg['db']['port'] = $Campsite['DATABASE_SERVER_PORT'];
$CampCfg['db']['name'] = $Campsite['DATABASE_NAME'];
$CampCfg['db']['user'] = $Campsite['DATABASE_USER'];
$CampCfg['db']['pass'] = $Campsite['DATABASE_PASSWORD'];

/** Cache settings **/
$CampCfg['cache']['enabled'] = true;
$CampCfg['cache']['expiration_time'] = 900;
$CampCfg['cache']['path'] = null;

/** Smarty settings **/
$CampCfg['smarty']['caching'] = false;
$CampCfg['smarty']['debugging'] = false;
$CampCfg['smarty']['force_compile'] = true;
$CampCfg['smarty']['compile_check'] = false;
$CampCfg['smarty']['use_subdirs'] = false;
$CampCfg['smarty']['left_delimeter'] = '{{';
$CampCfg['smarty']['right_delimeter'] = '}}';

/** Mailer settings **/
$CampCfg['smtp']['host'] = $Campsite['SMTP_SERVER_ADDRESS'];
$CampCfg['smtp']['port'] = $Campsite['SMTP_SERVER_PORT'];

?>