<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once $GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php";
$translator = \Zend_Registry::get('container')->getService('translator');

$f_language_selected = (int) camp_session_get('f_language_selected', 0);

$success = false;
$message = $translator->trans('Access denied.', array(), 'library'); // default error
$hiperlink = '';

$affectedArticles = 0;
$notAffectedArticles = 0;;

$message = '';
$errorMessage = '';

$articleCodes = array();
$flatArticleCodes = array();
$groupedArticleCodes = array();
foreach ($f_items as $articleCode) {
    list($articleId, $languageId) = explode('_', $articleCode);
    $articleCodes[] = array("article_id" => $articleId, "language_id" => $languageId);
    $flatArticleCodes[] = $articleId . '_' . $languageId;
    $groupedArticleCodes[$articleId][$languageId] = $languageId;
}

/*
function returnJson($status = 'true', $message = 'Articles updated.', $hiperlink = '') {
	$returnJson = array();
	$returnJson['status'] = $status;
	$returnJson['message'] = $message;
	$returnJson['hiperlink'] = $hiperlink;
	return json_encode($returnJson);
}
*/

/**
 * @param string $status
 */
function buildMessage($status, $no, $message) {
	$messageArray = array();
	$messageArray['status'] = $status;
	$messageArray['no'] = $no;
	$messageArray['textMessage'] = $message;
	return $messageArray;

}

function returnJson($affectedNo, $message, $notAffectedNo, $errorMessage, $hiperlink) {
	$returnJson = array();
	$returnJson['hiperlink'] = $hiperlink;

	$returnJson['messages'][] = buildMessage('affected', $affectedNo, $message);
	$returnJson['messages'][] = buildMessage('notAffected', $notAffectedNo, $errorMessage);
	return json_encode($returnJson);
}

switch($f_action) {
case 'delete':
    if (!$g_user->hasPermission('DeleteArticle')) {
        $success = false;
        $data->error = $translator->trans('You do not have the right to delete articles.', array(), 'library');
        break;
    }
    $affectedArticles = 0;
    $notAffectedArticles = 0;
    foreach ($articleCodes as $articleCode) {
        $article = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($article->delete()) {
            $success = true;
            $affectedArticles += 1;
        } else {
        	$notAffectedArticles += 1;
        }
    }



    $message = $translator->trans("$1 articles have been removed", array('$1' => $affectedArticles), 'library');
    $errorMessage = $translator->trans("$1 articles have not been removed", array('$1' => $notAffectedArticles), 'library');


    break;
case "workflow_publish":
    if (!$g_user->hasPermission('Publish')) {
        $success = false;
        $data->error = $translator->trans('You do not have the right to change this article status. Once submitted an article can only be changed by authorized users.', array(), 'library');
        break;
    }
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($articleObj->setWorkflowStatus('Y')) {
            $success = true;
            $affectedArticles += 1;
        } else {
        	$notAffectedArticles += 1;
        }
    }

    $message = $translator->trans("Article status set to $1 for $2 articles", array('$1' => $translator->trans("Published"), '$2' => $affectedArticles), 'library');
    $errorMessage = $translator->trans("Article status not set to $1 for $2 articles",  array('$1' => $translator->trans("Published"), '$2' => $notAffectedArticles), 'library');


    break;
case 'workflow_submit':
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($g_user->hasPermission("Publish") || $articleObj->userCanModify($g_user)) {
            if ($articleObj->setWorkflowStatus('S')) {
                $success = true;
                $affectedArticles += 1;
            } else {
	        	$notAffectedArticles += 1;
	        }
        } else {
        	$notAffectedArticles += 1;
        }
    }

    $message = $translator->trans("Article status set to $1 for $2 articles", array('$1' => $translator->trans("Submitted"), '$2' => $affectedArticles), 'library');
    $errorMessage = $translator->trans("Article status not set to $1 for $2 articles", array('$1' => $translator->trans("Submitted"), '$2' => $notAffectedArticles), 'library');


    break;
case 'workflow_new':
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($g_user->hasPermission("Publish")
                || ($g_user->hasPermission('ChangeArticle') && ($articleObj->getWorkflowStatus() == 'S'))) {
            if ($articleObj->setWorkflowStatus('N')) {
                $success = true;
                $affectedArticles += 1;
            } else {
	        	$notAffectedArticles += 1;
	        }
        } else {
	        	$notAffectedArticles += 1;
	        }
    }

    $message = $translator->trans("Article status set to $1 for $2 articles", array('$1' => $translator->trans("New"), '$2' => $affectedArticles), 'library');
    $errorMessage = $translator->trans("Article status set to $1 for $2 articles", array('$1' => $translator->trans("New"), '$2' => $affectedArticles), 'library');

    break;
case 'switch_onfrontpage':
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($articleObj->userCanModify($g_user)) {
            if ($articleObj->setOnFrontPage(!$articleObj->onFrontPage())) {
                $success = true;
                $affectedArticles += 1;
            } else {
	        	$notAffectedArticles += 1;
	        }
        } else {
        	$notAffectedArticles += 1;
        }
    }

    $message = $translator->trans("$1 toggled for $2 articles.", array('$1' => "&quot;".$translator->trans("On Front Page")."&quot;", '$2' => $affectedArticles), 'library');
    $errorMessage = $translator->trans("$1 not toggled for $2 articles.", array('$1' => "&quot;".$translator->trans("On Front Page")."&quot;", '$2' => $notAffectedArticles), 'library');

    break;
case 'switch_onsectionpage':

	$affectedArticles = 0;
	$notAffectedArticles = 0;

    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($articleObj->userCanModify($g_user)) {
            if ($articleObj->setOnSectionPage(!$articleObj->onSectionPage())) {
                $success = true;
                $affectedArticles += 1;
            } else {
            	$notAffectedArticles += 1;
            }
        } else {
        	$notAffectedArticles += 1;
        }
    }

    $message = $translator->trans("$1 toggled for $2 articles.", array('$1' => "&quot;".$translator->trans("On Section Page")."&quot;", '$2' => $affectedArticles), 'library');
    $errorMessage = $translator->trans("$1 not toggled for $2  articles.", array('$1' => "&quot;".$translator->trans("On Section Page")."&quot;", '$2' => $notAffectedArticles), 'library');


    break;
case 'switch_comments':
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($articleObj->userCanModify($g_user)) {
            if ($articleObj->setCommentsEnabled(!$articleObj->commentsEnabled())) {
                $success = true;
                $affectedArticles += 1;
            } else {
            	$notAffectedArticles += 1;
            }
        } else {
            $notAffectedArticles += 1;
        }
    }

    $message = $translator->trans("$1 toggled for $2 articles.", array('$1' => "&quot;".$translator->trans("Comments")."&quot;", '$2' => $affectedArticles), 'library');
    $errorMessage = $translator->trans("$1 not toggled for $2 articles.", array('$1' => "&quot;".$translator->trans("Comments")."&quot;", '$2' => $notAffectedArticles), 'library');

    break;
case 'unlock':
    foreach ($articleCodes as $articleCode) {
        $articleObj = new Article($articleCode['language_id'], $articleCode['article_id']);
        if ($articleObj->userCanModify($g_user)) {
            $articleObj->setIsLocked(false);
            $success = true;
            $affectedArticles += 1;
        } else {
           	$notAffectedArticles += 1;
        }
    }

    $message = $translator->trans("$1 Article(s) unlocked", array('$1' => $affectedArticles), 'library');
    $errorMessage = $translator->trans("$1 Article(s) not unlocked", array('$1' => $notAffectedArticles), 'library');

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
        $affectedArticles += 1;
    }

    $message = $translator->trans("$1 Article(s) duplicated", array('$1' => $affectedArticles), 'library');

    break;

case 'duplicate_interactive':
case 'move':
    $args = array_merge($_REQUEST, $f_params);
    unset($args["f_article_code"]);
    $argsStr = camp_implode_keys_and_values($args, "=", "&");
    $argsStr .= '&f_mode=multi&f_action=';
    $argsStr .= $f_action == 'move' ? 'move' : 'duplicate';
    $argsStr .= '&f_language_selected=' . ( (int) camp_session_get('f_language_selected', 0));
    foreach ($flatArticleCodes as $articleCode) {
        $argsStr .= '&f_article_code[]=' . $articleCode;
    }

	return returnJson(0, '', 0, '',  $Campsite['WEBSITE_URL'] . "/admin/articles/duplicate.php?".$argsStr);
    break;

case 'publish_schedule':
    $args = array_merge($_REQUEST, $f_params);
    $argsStr = camp_implode_keys_and_values($args, "=", "&");
    $argsStr .= '&f_language_selected=' . ( (int) camp_session_get('f_language_selected', 0));
    foreach ($flatArticleCodes as $articleCode) {
        $argsStr .= '&f_article_code[]=' . $articleCode;
    }
    return returnJson(0, '', 0, '',  $Campsite['WEBSITE_URL'] . "/admin/articles/multi_autopublish.php?".$argsStr);
    break;
}



if ($f_target == 'art_ofp') {
    $value = ($f_value == 'Yes') ? true : false;
    $success = $articleObj->setOnFrontPage($value);
    $message = $translator->trans("$1 toggled.", array('$1' => "&quot;".$translator->trans("On Front Page")."&quot;"), 'library');
}
if ($f_target == 'art_osp') {
    $value = ($f_value == 'Yes') ? true : false;
    $success = $articleObj->setOnSectionPage($value);
    $message = $translator->trans("$1 toggled.", array('$1' => "&quot;".$translator->trans("On Section Page")."&quot;"), 'library');
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

        $message = $translator->trans("Article status set to $1", array('$1' => $articleObj->getWorkflowDisplayString($f_value)), 'library');
    }
}

if($affectedArticles == 0 && $success) {
	$affectedArticles = 1;
}

return returnJson($affectedArticles, $message, $notAffectedArticles, $errorMessage, $hiperlink);
