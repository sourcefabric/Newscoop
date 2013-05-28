<?php
/**
 * @package Campsite
 *
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */


$upgrade_trigger_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'upgrading.php';
if (!file_exists($upgrade_trigger_path)) {
    header('Location: index.php');
    exit(0);
}

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/application.php';

// removes library/Zend in favor of vendor
$libZend = __DIR__ . '/library/Zend';
if (file_exists($libZend)) {
    exec("rm -rf $libZend", $output = array(), $code);
    if ($code) {
        echo 'Upgrade script could not remove ' . $libZend . '.
            Please do it manually and run this script again.';
    }
}

$application->bootstrap('autoloader');
$application->bootstrap('container');

header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");

$g_documentRoot = dirname(__FILE__);

// goes to install process if configuration files does not exist yet
if (!file_exists($g_documentRoot.'/conf/configuration.php')
        || !file_exists($g_documentRoot.'/conf/database_conf.php')) {
    header('Location: install/index.php');
    exit(0);
}

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/include/campsite_init.php');
require_once($g_documentRoot.'/bin/cli_script_lib.php');
require_once($g_documentRoot.'/install/classes/CampInstallation.php');
require_once($g_documentRoot.'/classes/User.php');
require_once($g_documentRoot.'/classes/CampPlugin.php');

set_time_limit(0);
$dbVersion = '';
$dbRoll = '';
$res = camp_detect_database_version($Campsite['DATABASE_NAME'], $dbVersion, $dbRoll);
if ($res !== 0) {
    $dbVersion = '[unknown]';
}
$dbInfo = $dbVersion;
if (!in_array($dbRoll, array('', '.'))) {
    $dbInfo .= ', roll ' . $dbRoll;
}
echo "Upgrading the database from version $dbInfo...";

// initiates the campsite site
$campsite = new CampSite();

// loads site configuration settings
$campsite->loadConfiguration(CS_PATH_CONFIG.DIR_SEP.'configuration.php');

// starts the session
$campsite->initSession();

$session = CampSite::GetSessionInstance();
$configDb = array('hostname'=>$Campsite['db']['host'],
                  'hostport'=>$Campsite['db']['port'],
                  'username'=>$Campsite['db']['user'],
                  'userpass'=>$Campsite['db']['pass'],
                  'database'=>$Campsite['db']['name']);
$session->setData('config.db', $configDb, 'installation');

// upgrading the database
$res = camp_upgrade_database($Campsite['DATABASE_NAME'], true, true);
if ($res !== 0) {
    display_upgrade_error("While upgrading the database: $res");
}
CampCache::singleton()->clear('user');
CampCache::singleton()->clear();
SystemPref::DeleteSystemPrefsFromCache();

// update plugins
CampPlugin::OnUpgrade();

CampRequest::SetVar('step', 'finish');
$install = new CampInstallation();
$install->initSession();
$step = $install->execute();

// update plugins environment
CampPlugin::OnAfterUpgrade();

CampTemplate::singleton()->clearCache();

if (file_exists($upgrade_trigger_path)) {
    @unlink($upgrade_trigger_path);
}

function display_upgrade_error($errorMessage, $exit = TRUE)
{
    if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'development') {
        var_dump($errorMessage);
        if ($exit) {
            exit(1);
        }
    }

    $template = CS_SYS_TEMPLATES_DIR.DIR_SEP.'_campsite_error.tpl';
    $templates_dir = CS_TEMPLATES_DIR;
    $params = array('context' => null,
                    'template' => $template,
                    'templates_dir' => $templates_dir,
                    'error_message' => $errorMessage
    );
    $document = CampSite::GetHTMLDocumentInstance();
    $document->render($params);
    if ($exit) exit(0);
}
?>
<p>finished<br/><a href="<?php echo "/$ADMIN";?>">Administration</a></p>
