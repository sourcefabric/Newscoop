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

// goes to install process if configuration files does not exist yet
if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/conf/configuration.php')
        || !file_exists($_SERVER['DOCUMENT_ROOT'].'/conf/database_conf.php')) {
    header('Location: /install/index.php');
}

define('CS_INSTALL_DIR', dirname(__FILE__) . '/install');

$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/include/campsite_init.php');
require_once($g_documentRoot.'/bin/cli_script_lib.php');
require_once($g_documentRoot.'/install/classes/CampInstallation.php');

function display_upgrade_error($p_errorMessage) {
    $template = '_campsite_error.tpl';
    $templates_dir = CS_SYS_TEMPLATES_DIR;
    $params = array('context' => null,
                    'template' => $template,
                    'templates_dir' => $templates_dir,
                    'error_message' => $p_errorMessage
    );
    $document = CampSite::GetHTMLDocumentInstance();
    $document->render($params);
    exit(0);
}

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
$res = camp_upgrade_database($Campsite['DATABASE_NAME'], true);
if ($res !== 0) {
    display_upgrade_error("While upgrading the database: $res");
}

CampRequest::SetVar('step', 'finish');

$install = new CampInstallation();

$install->initSession();

$step = $install->execute();

@unlink('admin.php');
@unlink('index.php');
$copyAdmin = copy('install/scripts/admin.php', 'admin.php');
$copyIndex = copy('install/scripts/index.php', 'index.php');
if (!$copyAdmin || !$copyIndex) {
    display_upgrade_error("while upgrading the database: Can't create the index.php file.");
}

$forward = $session->getData('forward');
$session->unsetData('forward');
header("Location: " . $forward);

unlink('upgrade.php');

?>
