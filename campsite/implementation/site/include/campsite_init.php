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

// redirects to the installation process if necessary
if (!file_exists($g_documentRoot.'/conf/configuration.php')
        || !file_exists($g_documentRoot.'/conf/database_conf.php')) {
    header('Location: /install/index.php');
}

require_once($g_documentRoot.'/include/campsite_constants.php');
require_once($g_documentRoot.'/conf/configuration.php');
require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/template_engine/classes/CampSite.php');
require_once($g_documentRoot.'/admin-files/lib_campsite.php');

?>