<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(dirname(__FILE__) . '/../include'),
    get_include_path(),
)));
if (!is_file('Zend/Application.php')) {
	// include libzend if we dont have zend_application
	set_include_path(implode(PATH_SEPARATOR, array(
		'/usr/share/php/libzend-framework-php',
		get_include_path(),
	)));
}
require_once 'Zend/Application.php';

$oldErrorReporting = error_reporting();
error_reporting(0);

if(!class_exists('Zend_Application',false)) {
    die('Missing dependency! Please install Zend Framework library!');
}
error_reporting($oldErrorReporting);

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap('autoloader');

$GLOBALS['g_campsiteDir'] = dirname(dirname(__FILE__));
require_once($GLOBALS['g_campsiteDir'].'/include/campsite_constants.php');
require_once($GLOBALS['g_campsiteDir'].'/install/classes/CampInstallation.php');
require_once(CS_PATH_CONFIG.DIR_SEP.'install_conf.php');

if (file_exists(CS_PATH_CONFIG.DIR_SEP.'configuration.php')
        && file_exists(CS_PATH_CONFIG.DIR_SEP.'database_conf.php')) {
    header("Location: ".CS_PATH_BASE_URL.str_replace('/install', '', $Campsite['SUBDIR']));
}

// check if template cache dir is writable
$templates_cache = dirname(dirname(__FILE__)) . DIR_SEP . 'cache';
if (!is_writable($templates_cache)) {
    echo '<!DOCTYPE html>';
    echo '<html><head><meta charset="utf-8" />';
    echo '<title>Install requirement</title>';
    echo '</head><body>';
    echo '<h1>Install requirement</h1>';
    echo "<p>Directory '$templates_cache' is not writable.</p>";
    echo "<p>Please make it writable in order to continue. (i.e. <code>$ sudo chmod o+w $templates_cache</code> on linux)</p>";
    echo '</body></html>';
    exit;
}
unset($templates_cache);

$install = new CampInstallation();

$install->initSession();

$step = $install->execute();

$install->dispatch($step);

$install->render();

if ($step == 'finish') {
	$template = CampTemplate::singleton();
	$template->clear_compiled_tpl();
}

?>
