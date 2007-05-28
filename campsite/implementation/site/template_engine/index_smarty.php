<?php

header("Content-type: text/html; charset=UTF-8");

global $_SERVER;
global $Campsite;
global $DEBUG;

// initialize needed global variables
$_SERVER['DOCUMENT_ROOT'] = getenv("DOCUMENT_ROOT");

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/liveuser_configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/parser_utils.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/SyntaxError.php');

// Meta classes
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/CampContext.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaLanguage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaPublication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaIssue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaSection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaArticle.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaImage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaAttachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaAudioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaComment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaTopic.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaUser.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaTemplate.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaSubscription.php');

// Campsite template class (Smarty extended)
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/CampTemplate.php');


$g_errorList = array();

function templateErrorHandler($p_errorCode, $p_errorString, $p_errorFile = null,
							  $p_errorLine = null, $p_errorContext = null)
{
	global $g_errorList;

	if (strncasecmp($p_errorString, 'Campsite error:', strlen("Campsite error:")) != 0
		&& strncasecmp($p_errorString, 'Smarty error:' ,strlen('Smarty error:')) != 0) {
		return;
	}

	$errorString = substr($p_errorString, strlen("Smarty error:"));
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
		$errorCode = SYNTAX_ERROR_INVALID_VALUE;
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


$context =& new CampContext();


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


// Image object
$context->image = new MetaImage(11);


// Article attachment object
$context->attachment = new MetaAttachment(3);


// Topic object
$context->topic = new MetaTopic(14);


// User object
$context->user = new MetaUser(1);


// Audioclip object
$context->audioclip = new MetaAudioclip('7426a84892288c93');


// Article comment
$context->comment = new MetaComment(2);


// Template object
$context->template = new MetaTemplate(101);


// Subscription object
$context->subscription = new MetaSubscription(5);


$tpl->assign('campsite', $context);
//$tpl->debugging = true;


/**** Exception test ****/
try {
	$articleObj =& new MetaArticle(1, 143);
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
