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

$start0 = microtime(true);

header("Content-type: text/html; charset=UTF-8");

$GLOBALS['g_campsiteDir'] = dirname(__FILE__);

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include'
.DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_CONFIG.DIR_SEP.'install_conf.php');

// goes to install process if configuration files does not exist yet
if (!file_exists(CS_PATH_CONFIG.DIR_SEP.'configuration.php')
|| !file_exists(CS_PATH_CONFIG.DIR_SEP.'database_conf.php')) {
    header('Location: '.$Campsite['SUBDIR'].'/install/');
    exit(0);
}

require_once(CS_PATH_INCLUDES.DIR_SEP.'campsite_init.php');

if (file_exists(CS_PATH_SITE . DIR_SEP . 'reset_cache')) {
    CampCache::singleton()->clear('user');
    @unlink(CS_PATH_SITE . DIR_SEP . 'reset_cache');
}

$start1 = microtime(true);
// initializes the campsite object
$campsite = new CampSite();

$start2 = microtime(true);
// loads site configuration settings
$campsite->loadConfiguration(CS_PATH_CONFIG.DIR_SEP.'configuration.php');

$start3 = microtime(true);
// starts the session
$campsite->initSession();

if (file_exists(CS_PATH_SITE.DIR_SEP.'upgrade.php')) {
    camp_upgrade();
    exit(0);
}

// checking whether it is a statistics (js based) request
$stats_only = false;
$campsite->processStats($stats_only);
if ($stats_only) {
    exit(0);
}

$start4 = microtime(true);
// initiates the context
$campsite->init();

$start5 = microtime(true);
// dispatches campsite
$campsite->dispatch();

$start6 = microtime(true);
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

$start7 = microtime(true);
// renders the site
$campsite->render();

$end1 = microtime(true);
// triggers an event after displaying
$campsite->event('afterRender');

$end2 = microtime(true);

// run internal cron scheduler
if (SystemPref::Get('ExternalCronManagement') == 'N') {
    flush();
    camp_cron();
}

function camp_upgrade()
{
    header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");

    camp_display_message("The website you are trying to view is currently down for maintenance.
        <br>Normal service will resume shortly.");
    echo '<META HTTP-EQUIV="Refresh" content="10">';
}

$initPaths = sprintf("%.3f", ($start1 - $start0)*1000);
$initCampsite = sprintf("%.3f", ($start2 - $start1)*1000);
$loadConfig = sprintf("%.3f", ($start3 - $start2)*1000);
$initSession = sprintf("%.3f", ($start4 - $start3)*1000);
$initContext = sprintf("%.3f", ($start5 - $start4)*1000);
$dispatch = sprintf("%.3f", ($start6 - $start5)*1000);
$loadTranslation = sprintf("%.3f", ($start7 - $start6)*1000);

$initTime = sprintf("%.3f", ($start7 - $start0)*1000);
$renderTemplate = sprintf("%.3f", ($end1 - $start7)*1000);
$endEvent = sprintf("%.3f", ($end2 - $end1)*1000);
$totalTime = sprintf("%.3f", ($end2 - $start0)*1000);
?>

<hr/>
<table>
<tr>

<td style="width: 300px; text-align: left; vertical-align: top">
<table style="border-style: ridge; border-right-style: ridge; border-width: thin; padding: 5px;">
<tr><th>Event</th><th>Time (msec)</th></tr>
<tr><td colspan="2"><hr/></td></tr>
<tr><td>init:</td><td style="text-align: right"><?php echo $initTime; ?></td></tr>
<tr><td>template render:</td><td style="text-align: right"><?php echo $renderTemplate; ?></td></tr>
<tr><td>end event:</td><td style="text-align: right"><?php echo $endEvent; ?></td></tr>
<tr><td colspan="2"><hr/></td></tr>
<tr><th>total:</th><th style="text-align: right"><?php echo $totalTime; ?></th></tr>
</table>
</td>

<td style="width: 220px; text-align: left; vertical-align: top">
<table style="border-style: ridge; border-right-style: ridge; border-width: thin; padding: 5px;">
<tr><th colspan="2">Cache</th></tr>
<tr><td colspan="2"><hr/></td></tr>
<tr><td>store requests:</td><td style="text-align: right"><?php echo CampCache::GetStoreRequests(); ?></td></tr>
<tr><td>fetch requests:</td><td style="text-align: right"><?php echo CampCache::GetFetchRequests(); ?></td></tr>
<tr><td>hits:</td><td style="text-align: right"><?php echo CampCache::GetHits(); ?></td></tr>
</table>
</td>

<td style="width: 300px; text-align: left; vertical-align: top">
<table style="border-style: ridge; border-right-style: ridge; border-width: thin; padding: 5px;">
<tr><th>Init Event</th><th>Time (msec)</th></tr>
<tr><td colspan="2"><hr/></td></tr>
<tr><td>load base init:</td><td style="text-align: right"><?php echo $initPaths; ?></td></tr>
<!-- <tr><td>campsite object:</td><td style="text-align: right"><?php echo $initCampsite; ?></td></tr> -->
<tr><td>load config:</td><td style="text-align: right"><?php echo $loadConfig; ?></td></tr>
<!-- <tr><td>session object:</td><td style="text-align: right"><?php echo $initSession; ?></td></tr> -->
<tr><td>context object:</td><td style="text-align: right"><?php echo $initContext; ?></td></tr>
<tr><td>dispatch:</td><td style="text-align: right"><?php echo $dispatch; ?></td></tr>
<tr><td>load translation:</td><td style="text-align: right"><?php echo $loadTranslation; ?></td></tr>
</table>
</td>

</tr>
</table>
