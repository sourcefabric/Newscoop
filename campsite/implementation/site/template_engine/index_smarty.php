<?php

header("Content-type: text/html; charset=UTF-8");

global $_SERVER;
global $Campsite;
global $DEBUG;

// initialize needed global variables
$_SERVER['DOCUMENT_ROOT'] = getenv("DOCUMENT_ROOT");

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/parser_utils.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');

// Meta classes
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaPublication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaIssue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaSection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaArticle.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaAttachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaUser.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaLanguage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaTopic.php');

// Campsite template class (Smarty extended)
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/CampTemplate.php');

// Smarty instance
$tpl = new CampTemplate();


// Topic object
$tpl->assign('topic', new MetaTopic(14));


// Language object
$tpl->assign('language', new MetaLanguage(1));


// Publication object
$tpl->assign('publication', new MetaPublication(6));


// Issue object
$tpl->assign('issue', new MetaIssue(6, 1, 1));


// Section object
$tpl->assign('section', new MetaSection(6, 1, 1, 1));


// Article object
$articleObj = new MetaArticle(1, 143);
//$tpl->register_object('article', $articleObj);
$tpl->assign('article', $articleObj);


// Article attachment object
$tpl->assign('articleAttachment', new MetaAttachment(1));


// User object
$userObj = new MetaUser(1);
if ($userObj->defined()) {
	$tpl->assign('user', $userObj);
}


/**** Exception test ****/
try {
    $articleObj->Name = 'test';
    echo "<h3>Set property test: failed</h3>.";
} catch (Exception $e) {
    echo "<h3>Set property test: success</h3>";
}


try {
	$tpl->display('camp_index.tpl');
} catch (InvalidPropertyException $e) {
	echo "<p>Invalid property " . $e->getProperty() . " of object " . $e->getClassName() . "</p>\n";
}

?>
