<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}


echo "<pre>";
print_r($_REQUEST);
echo "</pre>";

// Get input
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_codes = Input::Get('f_article_code', 'array', 0);
$f_article_list_action = Input::Get('f_article_list_action');
$f_article_offset = Input::Get('f_article_offset', 'int', 0, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

// Validate permissions
switch ($f_article_list_action) {
case "delete":
	if (!$User->hasPermission('DeleteArticle')) {
		camp_html_display_error(getGS("You do not have the right to delete articles."));
		exit;
	}
	break;
case "publish":
	if (!$User->hasPermission('Publish')) {
		$errorStr = getGS("You do not have the right to change this article status. Once submitted an article can only be changed by authorized users.");
		camp_html_display_error($errorStr, $BackLink);
		exit;	
	}
	break;
case "copy":
case "copy_interactive":
	if (!$User->hasPermission('AddArticle')) {
		$errorStr = getGS("You do not have the right to add articles.");
		camp_html_display_error($errorStr, $BackLink);
		exit;	
	}
	break;
}


$articleCodes = array();
$groupedArticleCodes = array();
foreach ($f_article_codes as $code) {
	list($articleId, $languageId) = split("_", $code);
	$articleCodes[] = array("article_id" => $articleId, "language_id" => $languageId);
	$groupedArticleCodes[$articleId][$languageId] = $languageId;
}

switch ($f_article_list_action) {
case "workflow_new":
	foreach ($articleCodes as $articleCode) {
		$articleObj =& new Article($articleCode['language_id'], $articleCode['article_id']);
		// A publisher can change the status in any way he sees fit.
		// Someone who can change an article can submit/unsubmit articles.
		if ($User->hasPermission('Publish')
			|| ($User->hasPermission('ChangeArticle') && ($articleObj->getPublished() == 'S'))) {
			$articleObj->setPublished('N');
		}
	}
	break;
case "workflow_submit":
	foreach ($articleCodes as $articleCode) {
		$articleObj =& new Article($articleCode['language_id'], $articleCode['article_id']);
		// A user who owns the article may submit it.
		if ($User->hasPermission("Publish") || $articleObj->userCanModify($User)) {
			$articleObj->setPublished('S');
		}
	}
	break;
case "workflow_publish":
	foreach ($articleCodes as $articleCode) {
		$articleObj =& new Article($articleCode['language_id'], $articleCode['article_id']);
		$articleObj->setPublished('Y');
	}
	break;
case "delete":
	foreach ($articleCodes as $articleCode) {
		$articleObj =& new Article($articleCode['language_id'], $articleCode['article_id']);
		$articleObj->delete();
	}
	break;
case "copy":
	foreach ($groupedArticleCodes as $articleNumber => $languageArray) {
		$languageId = camp_array_peek($languageArray);
		$articleObj =& new Article($languageId, $articleNumber);
		$articleObj->copy($articleObj->getPublicationId(), 
						  $articleObj->getIssueNumber(), 
						  $articleObj->getSectionNumber(),
						  null,
						  $languageArray);
	}
	break;
case "copy_interactive":
	$args = $_REQUEST;
	unset($args["f_article_code"]);
	$argsStr = camp_implode_keys_and_values($args, "=", "&");
	foreach ($_REQUEST["f_article_code"] as $code) {
		$argsStr .= "&f_article_code[]=$code";
	}
	$url = "Location: /$ADMIN/articles/duplicate.php?".$argsStr;
	header($url);
	exit;
case "unlock":
	foreach ($articleCodes as $articleCode) {
		$articleObj =& new Article($articleCode['language_id'], $articleCode['article_id']);
		if ($articleObj->userCanModify($User)) {
			$articleObj->unlock();
		}
	}
	break;
case "schedule_publish":
	$args = $_REQUEST;
	unset($args["f_article_code"]);
	$argsStr = camp_implode_keys_and_values($args, "=", "&");
	foreach ($_REQUEST["f_article_code"] as $code) {
		$argsStr .= "&f_article_code[]=$code";
	}
	$url = "Location: /$ADMIN/articles/multi_autopublish.php?".$argsStr;
	//echo $url;
	header($url);
	exit;
case "translate":
	$args = $_REQUEST;
	unset($args["f_article_code"]);
	$argsStr = camp_implode_keys_and_values($args, "=", "&");
	foreach ($_REQUEST["f_article_code"] as $code) {
		$argsStr .= "&f_article_code=$code";
		break;
	}
	$url = "Location: /$ADMIN/articles/translate.php?".$argsStr;
	//echo $url;
	header($url);
	exit;
}

header("Location: /$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id&f_article_offset=$f_article_offset");
exit;
?>