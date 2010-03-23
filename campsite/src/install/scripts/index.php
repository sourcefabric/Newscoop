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

$GLOBALS['g_campsiteDir'] = dirname(__FILE__);

header("Content-type: text/html; charset=UTF-8");

require_once($GLOBALS['g_campsiteDir'].'/include/campsite_init.php');

if (file_exists($GLOBALS['g_campsiteDir'] . '/reset_cache')) {
    CampCache::singleton()->clear('user');
    unlink($GLOBALS['g_campsiteDir'] . '/reset_cache');
}

// initializes the campsite object
$campsite = new CampSite();

// loads site configuration settings
$campsite->loadConfiguration(CS_PATH_CONFIG.DIR_SEP.'configuration.php');

// starts the session
$campsite->initSession();

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

?>