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

$start0 = microtime(true);

$g_documentRoot = dirname(__FILE__);
$g_campsiteDir = $g_documentRoot;

require_once($g_campsiteDir.'/include/campsite_init.php');

$start1 = microtime(true);
// initializes the campsite object
$campsite = new CampSite();

$start2 = microtime(true);
// triggers an event before render the page.
// looks for preview language if any.
$previewLang = $campsite->event('beforeRender');
if (!is_null($previewLang)) {
    require_once($g_campsiteDir.'/template_engine/classes/SyntaxError.php');
	set_error_handler('templateErrorHandler');

    // loads translations strings in the proper language for the error messages display
    camp_load_translation_strings('preview', $previewLang);
}

$start3 = microtime(true);
// loads site configuration settings
$campsite->loadConfiguration(CS_PATH_CONFIG.DIR_SEP.'configuration.php');

$start4 = microtime(true);
// starts the session
$campsite->initSession();

$start5 = microtime(true);
// initiates the context
$campsite->init();

$start6 = microtime(true);
// dispatches campsite
$campsite->dispatch();

$start7 = microtime(true);
// renders the site
$campsite->render();

$end1 = microtime(true);
// triggers an event after displaying
$campsite->event('afterRender');
$end2 = microtime(true);

$initPaths = $start1 - $start0;
$initCampsite = $start2 - $start1;
$loadTranslation = $start3 - $start2;
$loadConfig = $start4 - $start3;
$initSession = $start5 - $start4;
$initContext = $start6 - $start5;
$dispatch = $start7 - $start6;
$renderTemplate = $end1 - $start7;
$endEvent = $end2 - $end1;

echo "<h3>init paths: $initPaths sec</h3>\n";
echo "<h3>init campsite: $initCampsite sec</h3>\n";
echo "<h3>load translation: $loadTranslation sec</h3>\n";
echo "<h3>load config: $loadConfig sec</h3>\n";
//echo "<h3>init session: $initSession sec</h3>\n";
echo "<h3>init context: $initContext sec</h3>\n";
echo "<h3>dispatch: $dispatch sec</h3>\n";

$initTime = $start7 - $start0;
echo "<h3>init time: $initTime sec</h3>\n";
echo "<h3>template display time: $renderTemplate sec</h3>\n";
echo "<h3>end event: $endEvent sec</h3>\n";
$totalTime = $end2 - $start0;
echo "<h3>total time: $totalTime sec</h3>\n";

?>
