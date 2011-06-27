<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");


if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

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

$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	$BackLink = "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_language_id=$f_language_id&f_section_number=$f_section_number";
	camp_html_display_error(getGS('Article does not exist.'), $BackLink);
	exit;
}

switch ($f_action) {
	case "unlock":
		// If the user does not have permission to change the article
		// or they didnt create the article, give them the boot.
		if (!$articleObj->userCanModify($g_user)) {
			camp_html_add_msg(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users."));
		} else {
			camp_html_add_msg(getGS("Article unlocked."), "ok");
			$articleObj->setIsLocked(false);
		}
		camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, "edit.php", "", "&f_unlock=true"));
		exit;
	case "delete":
		if (!$g_user->hasPermission('DeleteArticle')) {
			camp_html_add_msg(getGS("You do not have the right to delete articles."));
			camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, "edit.php"));
		} else {
			$articleObj->delete();
			if ($f_publication_id > 0) {
				$url = "/$ADMIN/articles/index.php"
						."?f_publication_id=$f_publication_id"
						."&f_issue_number=$f_issue_number"
						."&f_section_number=$f_section_number"
						."&f_language_id=$f_language_id";
			} else {
				$url = "/$ADMIN/home.php";
			}
			camp_html_add_msg(getGS("Article deleted."), "ok");
			camp_html_goto_page($url);
		}
		exit;
	case "translate":
		$args = $_REQUEST;
		unset($args[SecurityToken::SECURITY_TOKEN]);
		$argsStr = camp_implode_keys_and_values($args, "=", "&");
		$argsStr .= "&f_article_code=".$f_article_number."_".$f_language_selected;
		camp_html_goto_page("/$ADMIN/articles/translate.php?".$argsStr, false);
		ArticleIndex::RunIndexer(3, 10, true);
		exit;
	case "copy":
		$args = $_REQUEST;
		unset($args[SecurityToken::SECURITY_TOKEN]);
		$argsStr = camp_implode_keys_and_values($args, "=", "&");
		$argsStr .= "&f_article_code[]=".$f_article_number."_".$f_language_selected;
		$argsStr .= "&f_mode=single&f_action=duplicate";
		camp_html_goto_page("/$ADMIN/articles/duplicate.php?".$argsStr, false);
		ArticleIndex::RunIndexer(3, 10, true);
		exit;
	case "move":
		$args = $_REQUEST;
		unset($args[SecurityToken::SECURITY_TOKEN]);
		$argsStr = camp_implode_keys_and_values($args, "=", "&");
		$argsStr .= "&f_article_code[]=".$f_article_number."_".$f_language_selected;
		$argsStr .= "&f_mode=single&f_action=move";
		camp_html_goto_page("/$ADMIN/articles/duplicate.php?".$argsStr, false);
		ArticleIndex::RunIndexer(3, 10, true);
		exit;
}

if (!is_null($f_action_workflow)) {
	$f_action_workflow = strtoupper($f_action_workflow);
	if (in_array($f_action_workflow, array('Y', 'M', 'S', 'N'))) {
		$access = false;
		// A publisher can change the status in any way he sees fit.
		// Someone who can change an article can submit/unsubmit articles.
		// A user who owns the article may submit it.
		if ($g_user->hasPermission('Publish')
			|| ($g_user->hasPermission('ChangeArticle') && ($f_action_workflow != 'Y'))
			|| ($articleObj->userCanModify($g_user) && ($f_action_workflow == 'S') )) {
			$access = true;
		}

		// If the article is set to New, remove all the autopublish events
		if ( $f_action_workflow == 'N') {
            $articleEvents = ArticlePublish::GetArticleEvents($f_article_number, $f_language_selected, TRUE);
            foreach($articleEvents as $event) {
                $eventId = $event->getArticlePublishId();
                $articlePublishObj = new ArticlePublish($eventId);
				if ($articlePublishObj->exists()) {
				    $articlePublishObj->delete();
				}
            }
		}

		if (!$access) {
			camp_html_add_msg(getGS("You do not have the right to change this article status. Once submitted an article can only be changed by authorized users."));
			camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, "edit.php"));
		}

		// If the article is not yet categorized, force it to be before publication.
		if (($f_action_workflow == "Y" || $f_action_workflow == 'M') && (($articleObj->getPublicationId() == 0) || ($articleObj->getIssueNumber() == 0) || ($articleObj->getSectionNumber() == 0))) {
			$args = $_REQUEST;
			$argsStr = camp_implode_keys_and_values($_REQUEST, "=", "&");
			$argsStr .= "&f_article_code[]=".$f_article_number."_".$f_language_selected;
			$argsStr .= "&f_mode=single&f_action=publish";
			camp_html_goto_page("/$ADMIN/articles/duplicate.php?".$argsStr);
		}

		$articleObj->setWorkflowStatus($f_action_workflow);
		camp_html_add_msg(getGS("Article status set to '$1'", $articleObj->getWorkflowDisplayString()), "ok");
	}
	$url = camp_html_article_url($articleObj, $f_language_id, "edit.php");
	camp_html_goto_page($url);
}

?>