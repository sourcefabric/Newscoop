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

header("Content-type: text/html; charset=UTF-8");

$GLOBALS['g_campsiteDir'] = dirname(__FILE__);

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include'
.DIRECTORY_SEPARATOR.'campsite_constants.php');

// goes to install process if configuration files does not exist yet
if (!file_exists(CS_PATH_CONFIG.DIR_SEP.'configuration.php')
|| !file_exists(CS_PATH_CONFIG.DIR_SEP.'database_conf.php')) {
    header('Location: /install/');
    exit(0);
}

require_once(CS_PATH_INCLUDES.DIR_SEP.'campsite_init.php');

if (file_exists(CS_PATH_SITE . DIR_SEP . 'reset_cache')) {
    CampCache::singleton()->clear('user');
    @unlink(CS_PATH_SITE . DIR_SEP . 'reset_cache');
}

// initializes the campsite object
$campsite = new CampSite();

// loads site configuration settings
$campsite->loadConfiguration(CS_PATH_CONFIG.DIR_SEP.'configuration.php');

// starts the session
$campsite->initSession();

if (file_exists(CS_PATH_SITE.DIR_SEP.'upgrade.php')) {
    camp_upgrade();
    exit(0);
}

// initiates the context
$campsite->init();

// dispatches campsite
$campsite->dispatch();

// triggers an event before render the page.
// looks for preview language if any.
$previewLang = $campsite->event('beforeRender');
if (!is_null($previewLang)) {
    require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/SyntaxError.php');
    set_error_handler('templateErrorHandler');

    // loads translations strings in the proper language for the error messages display
    camp_load_translation_strings('preview', $previewLang);
} else {
	set_error_handler(create_function('', 'return true;'));
}

// renders the site
$campsite->render();

// triggers an event after displaying
$campsite->event('afterRender');


function camp_upgrade()
{
    global $Campsite;

    require_once(CS_PATH_SITE.DIR_SEP.'bin'.DIR_SEP.'cli_script_lib.php');

    $lockFileName = $GLOBALS['g_campsiteDir'].DIR_SEP.'upgrade.php';
    $lockFile = fopen($lockFileName, "r");
    if ($lockFile === false) {
        camp_display_message("Unable to create single process lock control!");
    }
    if (!flock($lockFile, LOCK_EX | LOCK_NB)) { // do an exclusive lock
        camp_display_message("The upgrade process is already running.");
    }

    $res = camp_detect_database_version($Campsite['DATABASE_NAME'], $dbVersion);
    if ($res !== 0) {
        $dbVersion = '[unknown]';
    }

    camp_display_message("Upgrading the database from version $dbVersion...");
    echo '<META HTTP-EQUIV="Refresh" content="1;url=/upgrade.php">';

    flock($lockFile, LOCK_UN); // release the lock
}


function camp_display_message($p_message)
{
    $session = CampSite::GetSessionInstance();
    $forward = $session->setData('forward', $_SERVER['REQUEST_URI']);

    $params = array('context' => null,
                'template' => '_campsite_message.tpl',
                'templates_dir' => CS_PATH_SMARTY_SYS_TEMPLATES,
                'info_message' => $p_message
    );
    $document = CampSite::GetHTMLDocumentInstance();
    $document->render($params);
}

?>