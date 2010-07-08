<?php
header('Content-type: application/json');

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");

$f_article_id = Input::Get('articleid', 'int', 0);
$f_language_selected = 1;
$f_action = Input::Get('action', 'string', null, true);
$f_target = Input::Get('target', 'string', null, true);
$f_value = Input::Get('value', 'string', null, true);

$success = false;
$message = '';
$goto = '';

if (!Input::IsValid()) {

}

$articleObj = new Article($f_language_selected, $f_article_id);
//
if ($articleObj->userCanModify($g_user)) {

}

$articles = $_POST;
$articleCodes = array();
$flatArticleCodes = array();
$groupedArticleCodes = array();
foreach ($articles as $var => $articleCode) {
    if (strpos($var, 'row') === false) {
        continue;
    }
    list($articleId, $languageId) = explode("_", $articleCode);
    $articleCodes[] = array("article_id" => $articleId, "language_id" => $languageId);
    $flatArticleCodes[] = $articleId . '_' . $languageId;
    $groupedArticleCodes[$articleId][$languageId] = $languageId;
}


switch($f_action) {
case 'delete':
    if (!$g_user->hasPermission('DeleteArticle')) {
        $success = false;
        $data->error = getGS('You do not have the right to delete articles.');
        break;
    }
    $affectedArticles = 0;
    foreach ($articleCodes as $articleCode) {
        $article = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($article->delete()) {
            $success = true;
            $affectedArticles += 1;
        }
    }
    if ($success) {
        $message = getGS("$1 articles have been removed", $affectedArticles);
    }
    break;
case "workflow_publish":
    if (!$g_user->hasPermission('Publish')) {
        $success = false;
        $data->error = getGS('You do not have the right to change this article status. Once submitted an article can only be changed by authorized users.');
        break;
    }
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($articleObj->setWorkflowStatus('Y')) {
            $success = true;
            $affectedArticles += 1;
        }
    }
    if ($success) {
        $message = getGS("Article status set to '$1'", getGS("Published"));
    }
    break;
case 'workflow_submit':
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($g_user->hasPermission("Publish") || $articleObj->userCanModify($g_user)) {
            if ($articleObj->setWorkflowStatus('S')) {
                $success = true;
                $affectedArticles += 1;
            }
        }
    }
    if ($success) {
        $message = getGS("Article status set to '$1'", getGS("Submitted"));
    }    
    break;
case 'workflow_new':
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($g_user->hasPermission("Publish")
                || ($g_user->hasPermission('ChangeArticle') && ($articleObj->getWorkflowStatus() == 'S'))) {
            if ($articleObj->setWorkflowStatus('N')) {
                $success = true;
                $affectedArticles += 1;
            }
        }
    }
    if ($success) {
        $message = getGS("Article status set to '$1'", getGS("New"));
    }    
    break;
case 'switch_onfrontpage':
    foreach ($articleCodes as $articleCode) {
        if ($row == 'action') continue;
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($articleObj->userCanModify($g_user)) {
            if ($articleObj->setOnFrontPage(!$articleObj->onFrontPage())) {
                $success = true;
                $affectedArticles += 1;
            }
        }
    }
    if ($success) {
        $message = getGS("$1 toggled.", "&quot;".getGS("On Front Page")."&quot;");
    }    
    break;
case 'switch_onsectionpage':
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($articleObj->userCanModify($g_user)) {
            if ($articleObj->setOnSectionPage(!$articleObj->onSectionPage())) {
                $success = true;
                $affectedArticles += 1;
            }
        }
    }
    if ($success) {
        $message = getGS("$1 toggled.", "&quot;".getGS("On Section Page")."&quot;");
    }    
    break;
case 'switch_comments':
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($articleObj->userCanModify($g_user)) {
            if ($articleObj->setCommentsEnabled(!$articleObj->commentsEnabled())) {
                $success = true;
                $affectedArticles += 1;
            }
        }
    }
    if ($success) {
        $message = getGS("$1 toggled.", "&quot;".getGS("Comments")."&quot;");
    }    
    break;
case 'unlock':
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($articleObj->userCanModify($g_user)) {
            $articleObj->setIsLocked(false);
            $success = true;
            $affectedArticles += 1;
        }
    }
    if ($success) {
        $message = getGS("Article(s) unlocked");
    }
    break;
case 'duplicate':
    foreach ($groupedArticleCodes as $articleNumber => $languageArray) {
        $languageId = camp_array_peek($languageArray);
        $articleObj = new Article($languageId, $articleNumber);
        $articleObj->copy($articleObj->getPublicationId(),
                          $articleObj->getIssueNumber(),
                          $articleObj->getSectionNumber(),
                          $g_user->getUserId(),
                          $languageArray);
        $success = true;
    }
    if ($success) {
        $message = getGS("Article(s) duplicated");
    }
    break;
case 'duplicate_interactive':
    $args = $_REQUEST;
    unset($args["f_article_code"]);
    $argsStr = camp_implode_keys_and_values($args, "=", "&");
    $argsStr .= "&f_mode=multi&f_action=duplicate";
    foreach ($flatArticleCodes as $articleCode) {
        $argsStr .= '&f_article_code[]=' . $articleCode;
    }
    $goto = "/$ADMIN/articles/duplicate.php?".$argsStr;
    $success = true;
    break;
case 'move':
    $args = $_REQUEST;
    unset($args["f_article_code"]);
    $argsStr = camp_implode_keys_and_values($args, "=", "&");
    $argsStr .= "&f_mode=multi&f_action=move";
    foreach ($flatArticleCodes as $articleCode) {
        $argsStr .= '&f_article_code[]=' . $articleCode;
    }
    $goto = "/$ADMIN/articles/duplicate.php?".$argsStr;
    $success = true;
    break;
}



if ($f_target == 'art_ofp') {
    $value = ($f_value == 'Yes') ? true : false;
    $success = $articleObj->setOnFrontPage($value);
    $message = getGS("$1 toggled.", "&quot;".getGS("On Front Page")."&quot;");
}
if ($f_target == 'art_osp') {
    $value = ($f_value == 'Yes') ? true : false;
    $success = $articleObj->setOnSectionPage($value);
    $message = getGS("$1 toggled.", "&quot;".getGS("On Section Page")."&quot;");
}
if ($f_target == 'art_status') {
    if (in_array($f_value, array('Published', 'Submitted', 'New'))) {
        switch($f_value) {
        case 'New': $f_value = 'N'; break;
        case 'Published': $f_value = 'Y'; break;
        case 'Submitted': $f_value = 'S'; break;
        }
        $access = false;
        // A publisher can change the status in any way he sees fit.
        // Someone who can change an article can submit/unsubmit articles.
        // A user who owns the article may submit it.
        if ($g_user->hasPermission('Publish')
                || ($g_user->hasPermission('ChangeArticle') && ($f_value != 'Y'))
                || ($articleObj->userCanModify($g_user) && ($f_value == 'S') )) {
            $access = true;
        }

        // If the article is not yet categorized, force it to be before publication.
        if (($f_action_workflow == "Y")
                && (($articleObj->getPublicationId() == 0)
                || ($articleObj->getIssueNumber() == 0) || ($articleObj->getSectionNumber() == 0))) {
            //$args = $_REQUEST;
            //$argsStr = camp_implode_keys_and_values($_REQUEST, "=", "&");
            //$argsStr .= "&f_article_code[]=".$f_article_number."_".$f_language_selected;
            //$argsStr .= "&f_mode=single&f_action=publish";
            //camp_html_goto_page("/$ADMIN/articles/duplicate.php?".$argsStr);
        }

        $success = $articleObj->setWorkflowStatus($f_value);
        
        $message = getGS("Article status set to '$1'", $articleObj->getWorkflowDisplayString($f_value));
    }
}

require_once('php/JSON.php');    
$json = new Services_JSON();
echo($json->encode(array(
	"success" => $success,
	"message" => $message,
	"goto" => $goto,
)));
?>