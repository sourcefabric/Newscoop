<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_action = Input::Get('f_action', 'string', null, true);
$f_action_workflow = Input::Get('f_action_workflow', 'string', null, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

if (is_null($f_action) && is_null($f_action_workflow)) {
	camp_html_display_error(getGS('No action specified'));
	exit;	
}

$articleObj =& new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'), $BackLink);
	exit;		
}

switch ($f_action) {
	case "unlock": 
		// If the user does not have permission to change the article
		// or they didnt create the article, give them the boot.
		if (!$articleObj->userCanModify($User)) {
			camp_html_display_error(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users."));
			exit;	
		}
		$articleObj->unlock();
		header('Location: '.camp_html_article_url($articleObj, $f_language_selected, "edit.php", "", "&f_unlock=true"));
		exit;
	case "delete":
		if (!$User->hasPermission('DeleteArticle')) {
			camp_html_display_error(getGS("You do not have the right to delete articles."));
			exit;
		}
		$articleObj->delete();
		$url = "/$ADMIN/articles/index.php" 
				."?f_publication_id=$f_publication_id"
				."&f_issue_number=$f_issue_number"
				."&f_section_number=$f_section_number"
				."&f_language_id=$f_language_id";
		header("Location: $url");
		exit;
	case "translate":
		$args = $_REQUEST;
		$argsStr = camp_implode_keys_and_values($_REQUEST, "=", "&");
		$argsStr .= "&f_article_code=".$f_article_number."_".$f_language_selected;
		$url = "Location: /$ADMIN/articles/translate.php?".$argsStr;
		header($url);
		exit;
	case "copy":
		$args = $_REQUEST;
		$argsStr = camp_implode_keys_and_values($_REQUEST, "=", "&");
		$argsStr .= "&f_article_code[]=".$f_article_number."_".$f_language_selected;
		$argsStr .= "&f_mode=single";
		$url = "Location: /$ADMIN/articles/duplicate.php?".$argsStr;
		header($url);
		exit;
}

if (!is_null($f_action_workflow)) {
	$access = false;
	// A publisher can change the status in any way he sees fit.
	// Someone who can change an article can submit/unsubmit articles.
	// A user who owns the article may submit it.
	if ($User->hasPermission('Publish') 
		|| ($User->hasPermission('ChangeArticle') && ($f_action_workflow != 'Y'))
		|| ($articleObj->userCanModify($User) && ($f_action_workflow == 'S') )) {
		$access = true;
	}
	if (!$access) {
		$errorStr = getGS("You do not have the right to change this article status. Once submitted an article can only be changed by authorized users.");
		camp_html_display_error($errorStr);
		exit;	
	}
	
	$articleObj->setPublished($f_action_workflow);
	
	$url = camp_html_article_url($articleObj, $f_language_selected, "edit.php");
	header("Location: $url");	
	exit;
}

?>