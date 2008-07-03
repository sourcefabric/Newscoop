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

global $g_documentRoot;
global $Campsite;

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/include/campsite_init.php');
require_once($g_documentRoot.'/template_engine/classes/SyntaxError.php');


function templateErrorHandler($p_errorCode, $p_errorString, $p_errorFile = null,
							  $p_errorLine = null, $p_errorContext = null)
{
	global $g_errorList;

	if (strncasecmp($p_errorString, 'Campsite error:', strlen("Campsite error:")) == 0) {
		$errorString = substr($p_errorString, strlen("Campsite error:"));
	} elseif(strncasecmp($p_errorString, 'Smarty error:' ,strlen('Smarty error:')) == 0) {
		$errorString = substr($p_errorString, strlen("Smarty error:"));
	} else {
		return;
	}

	$what = null;

	if (preg_match('/unrecognized tag:?\s*\'?([^\(]*)\'?\s*\(/', $errorString, $matches)) {
		$errorCode = SYNTAX_ERROR_UNRECOGNIZED_TAG;
		$what = array($matches[1]);
	} elseif (preg_match('/(\$.+)\s+is\s+an\s+unknown\s+reference/', $errorString, $matches)) {
		$errorCode = SYNTAX_ERROR_UNKNOWN_REFERENCE;
		$what = array($matches[1]);
	} elseif (preg_match('/invalid\s+property\s+(.+)\s+of\s+object\s+(.*)/', $errorString, $matches)) {
		$errorCode = SYNTAX_ERROR_INVALID_PROPERTY;
		$what = array($matches[1], $matches[2]);
	} elseif (preg_match('/invalid\s+value\s+(.+)\s+of\s+property\s+(.*)\s+of\s+object\s+(.*)/', $errorString, $matches)) {
		$errorCode = SYNTAX_ERROR_INVALID_PROPERTY_VALUE;
		$what = array($matches[1], $matches[2], $matches[3]);
	} elseif (preg_match('/invalid\s+parameter\s+(.+)\s+in\s+(.*)/', $errorString, $matches)) {
		$errorCode = SYNTAX_ERROR_INVALID_PARAMETER;
		$what = array($matches[1], $matches[2]);
	} elseif (preg_match('/invalid\s+value\s+(.+)\s+of\s+parameter\s+(.*)\s+in\s+statement\s+(.*)/', $errorString, $matches)) {
		$errorCode = SYNTAX_ERROR_INVALID_PARAMETER_VALUE;
		$what = array($matches[1], $matches[2], $matches[3]);
	} elseif (preg_match('/missing\s+parameter\s+(.*)\s+in\s+statement\s+(.*)/', $errorString, $matches)) {
		$errorCode = SYNTAX_ERROR_MISSING_PARAMETER;
		$what = array($matches[1], $matches[2]);
	} elseif (preg_match('/invalid\s+operator\s+(.+)\s+of\s+parameter\s+(.*)\s+in\s+statement\s+(.*)/', $errorString, $matches)) {
		$errorCode = SYNTAX_ERROR_INVALID_OPERATOR;
		$what = array($matches[1], $matches[2], $matches[3]);
    } elseif (preg_match('/invalid\s+attribute\s+(.+)\s+in\s+statement\s+(.*),\s+(.*)\s+parameter/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_INVALID_ATTRIBUTE;
        $what = array($matches[1], $matches[2], $matches[3]);
    } elseif (preg_match('/invalid\s+template\s+(.*)\s+specified\s+in\s+the\s+(.*)\s+form/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_INVALID_TEMPLATE;
        $what = array($matches[1], $matches[2]);
    } else {
		$errorCode = SYNTAX_ERROR_UNKNOWN;
		$what = array($errorString);
	}

	if (preg_match('/\[in\s+([\d\w]*\.tpl)*\s+line\s+([\d]+)\s*\]/', $errorString, $matches)) {
		$errorFile = $matches[1];
		$errorLine = $matches[2];
	} else {
		$errorFile = null;
		$errorLine = null;
	}

	$error = new SyntaxError(SyntaxError::ConstructParameters($errorCode, $errorFile,
							 $errorLine, $what));
	$g_errorList[] = $error;
}


set_error_handler('templateErrorHandler');

// initiates the campsite site
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
// loads translations strings in the proper language
// for preview error messaging.
camp_load_translation_strings('preview', $previewLang);

// renders the site
$campsite->render();

// triggers an event after displaying
$campsite->event('afterRender');

?>
