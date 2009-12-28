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

$GLOBALS['g_campsiteDir'] = dirname(dirname(__FILE__));
require_once($GLOBALS['g_campsiteDir'].'/include/campsite_constants.php');
require_once($GLOBALS['g_campsiteDir'].'/install/classes/CampInstallation.php');

define('CS_INSTALL_DIR', dirname(__FILE__));

if (file_exists(CS_PATH_CONFIG.DIR_SEP.'configuration.php')
        && file_exists(CS_PATH_CONFIG.DIR_SEP.'database_conf.php')) {
    header("Location: ".CS_PATH_BASE_URL);
}

$install = new CampInstallation();

$install->initSession();

$step = $install->execute();
if ($step == 'finish') {
    $copyAdmin = @copy($GLOBALS['g_campsiteDir'].'/install/scripts/admin.php',
    $GLOBALS['g_campsiteDir'].'/admin.php');
    $copyIndex = @copy($GLOBALS['g_campsiteDir'].'/install/scripts/index.php',
    $GLOBALS['g_campsiteDir'].'/index.php');
    if ($copyAdmin && $copyIndex && file_exists($GLOBALS['g_campsiteDir'].'/upgrade.php')) {
        @unlink($GLOBALS['g_campsiteDir'].'/upgrade.php');
    }
}

$install->dispatch($step);

$install->render();

if ($step == 'finish') {
	$template = CampTemplate::singleton();
	$template->clear_compiled_tpl();
}

?>