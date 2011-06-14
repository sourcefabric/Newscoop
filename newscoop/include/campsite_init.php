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

if (!isset($GLOBALS['g_campsiteDir'])) {
    $GLOBALS['g_campsiteDir'] = dirname(dirname(__FILE__));
} 

// redirects to the installation process if necessary
if (!file_exists($GLOBALS['g_campsiteDir'].'/conf/configuration.php')
        || !file_exists($GLOBALS['g_campsiteDir'].'/conf/database_conf.php')) {
    header('Location: install/index.php');
    exit;
}

// set include path for local pear packages
set_include_path('/usr/share/php/libzend-framework-php' . PATH_SEPARATOR .
    get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once($GLOBALS['g_campsiteDir'].'/conf/configuration.php');
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampSite.php');
require_once($GLOBALS['g_campsiteDir'].'/admin-files/lib_campsite.php');

// set timezone
$timeZone = SystemPref::Get('TimeZone');
if (!empty($timeZone)) {
    $g_ado_db->Execute("SET SESSION time_zone = '" . $timeZone . ":00'");
    $timeZone[0] = $timeZone[0] == '-' ? '+' : '-';
    date_default_timezone_set('Etc/GMT' . $timeZone);
} else {
    // Some people forget to set their timezone in their php.ini,
    // this prevents that from generating warnings
    @date_default_timezone_set(@date_default_timezone_get());
}
unset($timeZone);

?>
