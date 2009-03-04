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

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/include/campsite_constants.php');
require_once($g_documentRoot.'/install/classes/CampInstallation.php');

define('CS_INSTALL_DIR', dirname(__FILE__));

if (file_exists(CS_PATH_CONFIG.DIR_SEP.'configuration.php')
        && file_exists(CS_PATH_CONFIG.DIR_SEP.'database_conf.php')) {
    header("Location: ".CS_PATH_BASE_URL);
}

$install = new CampInstallation();

$install->initSession();

$step = $install->execute();
if ($step == 'finish') {
    $copyAdmin = @copy($g_documentRoot.'/install/scripts/admin.php', $g_documentRoot.'/admin.php');
    $copyIndex = @copy($g_documentRoot.'/install/scripts/index.php', $g_documentRoot.'/index.php');
    if ($copyAdmin && $copyIndex && file_exists($g_documentRoot.'/upgrade.php')) {
        @unlink($g_documentRoot.'/upgrade.php');
    }
}

$install->dispatch($step);

$install->render();

if ($step == 'finish') {
	$template = CampTemplate::singleton();
	$template->clear_compiled_tpl();
}

?>