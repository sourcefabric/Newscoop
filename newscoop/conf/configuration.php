<?php

global $Campsite, $ADMIN_DIR;

$GLOBALS['g_campsiteDir'] = dirname(dirname(__FILE__));

require_once($GLOBALS['g_campsiteDir'].'/include/campsite_constants.php');

// sets the PEAR local directory
set_include_path(CS_PATH_SITE.'/library'.PATH_SEPARATOR.CS_PATH_PEAR_LOCAL.PATH_SEPARATOR.get_include_path());

require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampSession.php');


/** System settings **/
$Campsite['campsite']['url_default_type'] = 2;
$Campsite['campsite']['secret_key'] = SystemPref::Get('SiteSecretKey');
$Campsite['campsite']['session_lifetime'] = SystemPref::Get('SiteSessionLifeTime');

/** General settings **/
$Campsite['site']['online'] = SystemPref::Get('SiteOnline');
$Campsite['site']['title'] = SystemPref::Get('SiteTitle');
$Campsite['site']['keywords'] = SystemPref::Get('SiteMetaKeywords');
$Campsite['site']['description'] = SystemPref::Get('SiteMetaDescription');
$Campsite['site']['charset'] = 'utf-8';
$Campsite['site']['help_url'] = 'http://www.sourcefabric.org/en/products/newscoop_support/';
$Campsite['site']['about_url'] = 'http://www.sourcefabric.org/en/about/page/';
$Campsite['site']['email'] = 'newscoop-bug@sourcefabric.org';

$Campsite['sourcefabric']['url'] = 'http://www.sourcefabric.org';

/** Locale settings **/
$Campsite['locale']['lang_id'] = 1;
$Campsite['locale']['lang_iso'] = 'en-US';
$Campsite['locale']['lang_code'] = 'en';
$Campsite['locale']['lang_name'] = 'english';

/** Cache settings **/
$Campsite['cache']['enabled'] = true;
$Campsite['cache']['expiration_time'] = 900;
$Campsite['cache']['path'] = null;

/** Smarty settings **/
$Campsite['smarty']['debugging'] = false;
$Campsite['smarty']['force_compile'] = false;
$Campsite['smarty']['compile_check'] = true;
$Campsite['smarty']['use_subdirs'] = false;
$Campsite['smarty']['left_delimeter'] = '{{';
$Campsite['smarty']['right_delimeter'] = '}}';

/** File Archive Storage Server settings **/
$Campsite['xmlrpc_storage']['path'] = '/mma/xmlrpc/';
$Campsite['xmlrpc_storage']['file'] = 'xrLocStor.php';

/** Mailer settings **/
$Campsite['smtp']['host'] = SystemPref::Get('SMTPHost');
$Campsite['smtp']['port'] = SystemPref::Get('SMTPPort');
$Campsite['smtp']['default_host'] = 'localhost';
$Campsite['smtp']['default_port'] = 25;

/** Backward compatibility **/
$ADMIN_DIR = "admin-files";

require_once($GLOBALS['g_campsiteDir'].'/conf/database_conf.php');
require_once($GLOBALS['g_campsiteDir'].'/conf/install_conf.php');
