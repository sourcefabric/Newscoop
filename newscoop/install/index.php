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

// some installation parts tend to take long time
set_time_limit(0);

define('INSTALL', TRUE);
define('DONT_BOOTSTRAP_ZEND', TRUE);

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', __DIR__ . '/../application');

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

$GLOBALS['g_campsiteDir'] = dirname(dirname(__FILE__));

// check if template cache dir is writable
$templates_cache = dirname(dirname(__FILE__)) . '/cache';
if (!is_writable($templates_cache)) {
    echo '<!DOCTYPE html>';
    echo '<html><head><meta charset="utf-8" />';
    echo '<title>Install requirement</title>';
    echo '<link rel="shortcut icon" href="' . $GLOBALS['g_campsiteDir'] . '/admin-style/images/7773658c3ccbf03954b4dacb029b2229.ico" />';
    echo '</head><body>';
    echo '<h1>Install requirement</h1>';
    echo "<p>Directory '$templates_cache' is not writable.</p>";
    echo "<p>Please make it writable in order to continue. (i.e. <code>$ sudo chmod -R 777 $templates_cache</code> on linux)</p>";
    echo '</body></html>';
    exit;
}
unset($templates_cache);

require_once __DIR__ . '/../application.php';
$application->bootstrap('autoloader');

require_once($GLOBALS['g_campsiteDir'].'/include/campsite_constants.php');
require_once($GLOBALS['g_campsiteDir'].'/install/classes/CampInstallation.php');
require_once(CS_PATH_CONFIG.'/install_conf.php');

if (!file_exists(APPLICATION_PATH . '/../conf/installation.php')) {
    header("Location: ".CS_PATH_BASE_URL.str_replace('/install', '', $Campsite['SUBDIR']));
}

$install = new CampInstallation();

$install->initSession();

$step = $install->execute();

$install->dispatch($step);

$install->render();

if ($step == 'finish') {
    $template = CampTemplate::singleton();
    $template->clearCache();
}