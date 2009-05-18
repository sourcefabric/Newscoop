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

if (!isset($g_campsiteDir)) {
    $g_campsiteDir = dirname(dirname(__FILE__));
} 

// redirects to the installation process if necessary
if (!file_exists($g_campsiteDir.'/conf/configuration.php')
        || !file_exists($g_campsiteDir.'/conf/database_conf.php')) {
    header('Location: /install/index.php');
    exit;
}

require_once($g_campsiteDir.'/conf/configuration.php');
require_once($g_campsiteDir.'/db_connect.php');
require_once($g_campsiteDir.'/template_engine/classes/CampSite.php');
require_once($g_campsiteDir.'/admin-files/lib_campsite.php');

?>