<?php

header("Content-type: text/html; charset=UTF-8");

global $_SERVER;
global $Campsite;
global $DEBUG;

// initialize needed global variables
$_SERVER['DOCUMENT_ROOT'] = getenv("DOCUMENT_ROOT");

require_once($_SERVER['DOCUMENT_ROOT'].'/include/campsite_constants.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/liveuser_configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');


$g_errorList = array();

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


// Smarty instance
$tpl = CampTemplate::singleton();


$context = $tpl->context();

// Language object
$context->language = new MetaLanguage(1);


// Publication object
$context->publication = new MetaPublication(6);


// Issue object
$context->issue = new MetaIssue(6, 1, 1);


// Section object
$context->section = new MetaSection(6, 1, 1, 1);


// Article object
$context->article = new MetaArticle(1, 143);

// init the poll
CampPlugin::initPlugins();

$tpl->assign('campsite', $context);
#$tpl->debugging = true;


#set_error_handler('templateErrorHandler');

try {
	$tpl->display($_SERVER['DOCUMENT_ROOT'] .'/plugins/poll/template_engine/poll_index.tpl');
} catch (InvalidPropertyHandlerException $e) {
	echo "<p>Internal error: handler was not specified for property " . $e->getPropertyName()
		. " of object " . $e->getClassName() . "</p>\n";
}

if (!empty($g_errorList)) {
	echo "<p>Errors:</p>\n";
}
foreach ($g_errorList as $error) {
	echo "<p>" . $error->getMessage() . "</p>\n";
}

?>
