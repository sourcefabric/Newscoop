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

// initiates the campsite site
$campsite = new CampSite();

// loads site configuration settings
$campsite->loadConfiguration(CS_PATH_CONFIG.DIR_SEP.'configuration.php');

// starts the session
$campsite->initSession();

// upgrading the database
$res = camp_upgrade_database($Campsite['DATABASE_NAME']);
if ($res !== 0) {
    $res = "while upgrading the database: $res";
    $template = '_campsite_error.tpl';
    $templates_dir = CS_PATH_SMARTY_SYS_TEMPLATES;
    $params = array('context' => null,
                    'template' => $template,
                    'templates_dir' => $templates_dir,
                    'error_message' => $res
    );
    $document = CampSite::GetHTMLDocumentInstance();
    $document->render($params);
    exit(0);
}

CampRequest::SetVar('step', 'cron_jobs');

$install = new CampInstallation();

$install->initSession();

$step = $install->execute();

//header("Location: /");

?>