<?php

header("Content-type: text/html; charset=UTF-8");

global $Campsite;
global $DEBUG;

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


// Initialise the template and context
$_SERVER['REQUEST_URI'] = '';
$tpl = CampTemplate::singleton();

$context = $tpl->context();
$tpl->assign('campsite', $context);

// Language object
$context->language = new MetaLanguage(1);


// Publication object
$context->publication = new MetaPublication(1);


// Issue object
$context->issue = new MetaIssue(1, 1, 1);


// Section object
$context->section = new MetaSection(1, 1, 1, 10);


// Article object
$context->article = new MetaArticle(1, 140);


// Image object
$context->image = new MetaImage(1);


// Article attachment object
$context->attachment = new MetaAttachment(4);


// Topic object
$context->topic = new MetaTopic(14);


// User object
$context->user = new MetaUser(1);


// Audioclip object
$context->audioclip = new MetaAudioclip('7160d04166d69f50');


// Article comment
$context->comment = new MetaComment(9);


// Template object
$context->template = new MetaTemplate('index.tpl');


// Subscription object
$context->subscription = new MetaSubscription(5);

//$tpl->debugging = true;


/**** Exception test ****/
try {
	$articleObj = new MetaArticle(1, 143);
    $articleObj->Name = 'test';
    echo "<h3>Set property test: failed</h3>";
} catch (Exception $e) {
    echo "<h3>Set property test: success</h3>";
}

set_error_handler('templateErrorHandler');

try {
	$tpl->display('camp_index.tpl');
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
