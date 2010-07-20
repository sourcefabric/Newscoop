<?php
require_once($GLOBALS['g_campsiteDir']."/include/phorum_load.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/DbReplication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_forum.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_message.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_user.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleComment.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_comment_nickname = Input::Get('f_comment_nickname', 'string', '', true);
$f_comment_subject = Input::Get('f_comment_subject', 'string', '', true);
$f_comment_body = Input::Get('f_comment_body');
$f_comment_parent_id = Input::Get('f_comment_id', 'int', 0, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;
}

// Check that the article exists.
$articleObj = new Article($f_language_id, $f_article_number);
if (!$articleObj->exists()) {
    exit;
}
if (!$articleObj->commentsEnabled() || $articleObj->commentsLocked())  {
    camp_html_goto_page(camp_html_article_url($articleObj, $f_language_selected, "edit.php"));
}

if (SystemPref::Get("UseDBReplication") == 'Y') {
    $dbReplicationObj = new DbReplication();
    $connectedToOnlineServer = $dbReplicationObj->connect();
    if ($connectedToOnlineServer == false) {
        camp_html_add_msg(getGS("Comments Disabled: you are either offline or not able to reach the Online server"));
        camp_html_goto_page(camp_html_article_url($articleObj, $f_language_selected, "edit.php"));
    }
}

// Add the user if it doesnt exist in the Phorum user table
$phorumUser = new Phorum_user($g_user->getUserId());
if (!$phorumUser->CampUserExists($g_user->getUserId())) {
    $success = $phorumUser->create($g_user->getUserName(),
				   $g_user->getPassword(),
                                   $g_user->getEmail(),
                                   $g_user->getUserId());
}

// Get the forum ID.
$publicationObj = new Publication($articleObj->getPublicationId());
$forumId = $publicationObj->getForumId();

// Exit if the forum hasnt been created (this should never happen).
if (!$forumId) {
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_selected, "edit.php")."#add_comment");
}

// Create/get first post.
$firstPost = camp_comment_first_post($articleObj, $forumId);
// Exit if the forum hasnt been created (this should never happen).
if (!$firstPost->exists()) {
    camp_html_goto_page(camp_html_article_url($articleObj, $f_language_selected, "edit.php")."#add_comment");
}
$threadId = $firstPost->getThreadId();

// If reply isnt specified, then its a reply to the base message.
if ($f_comment_parent_id == 0) {
	$f_comment_parent_id = $firstPost->getMessageId();
}

// Create the comment
$commentObj = new Phorum_message();
$commentObj->create($forumId,
                    $f_comment_subject,
                    $f_comment_body,
                    $threadId,
                    $f_comment_parent_id,
                    $f_comment_nickname,
                    $g_user->getEmail(),
                    $g_user->getUserId());
$commentObj->setStatus(PHORUM_STATUS_APPROVED);

// Link the message to the article
$isFirstMessage = ($threadId == 0);
ArticleComment::Link($f_article_number, $f_language_id, $commentObj->getMessageId(), $isFirstMessage);

camp_html_goto_page(camp_html_article_url($articleObj, $f_language_selected, "edit.php")."#add_comment");


/**
 * Create the first message for an article, which is a blank message
 * with the title of the article as the subject.
 *
 * @param Article $p_article
 * @param int $p_forumId
 * @return mixed
 *      The comment created (or the one that already exists) on success,
 *      or false on error.
 */
function camp_comment_first_post($p_article, $p_forumId)
{
    // Check if the first post already exists.
    $articleNumber = $p_article->getArticleNumber();
    $languageId = $p_article->getLanguageId();
    $firstPostId = ArticleComment::GetCommentThreadId($articleNumber, $languageId);
    if ($firstPostId) {
        $firstPost = new Phorum_message($firstPostId);
        if ($firstPost->exists()) {
            return $firstPost;
        }
        // Unlink the message from the current article.
        ArticleComment::Unlink($articleNumber, $languageId, $firstPostId);
    }

    // Get article creator
    $user = new User($p_article->getCreatorId());
    if ($user->exists()) {
        $userId = $user->getUserId();
        $userEmail = $user->getEmail();
        $userPasswd = $user->getPassword();
        $userName = $user->getUserName();
        $userRealName = $user->getRealName();

        // Create phorum user if necessary
        $phorumUser = Phorum_user::GetByUserName($userName);
        if (!is_object($phorumUser)) {
            $phorumUser = new Phorum_user();
        }
        if (!$phorumUser->CampUserExists($userId)
            && !$phorumUser->create($userName, $userPasswd, $userEmail, $userId)) {
            return false;
        }
    } else {
        $userId = null;
        $userEmail = '';
        $userRealName = '';
    }

    // Create the comment.
    $title = $p_article->getTitle();
    $commentObj = new Phorum_message();
    if ($commentObj->create($p_forumId,
                               $title,
                               '',
                               0,
                               0,
                               $userRealName,
                               $userEmail,
                               is_null($userId) ? 0 : $userId)) {
        // Link the message to the current article.
        ArticleComment::Link($articleNumber, $languageId, $commentObj->getMessageId(), true);
        return $commentObj;
    } else {
        return false;
    }
} // fn camp_comment_first_post

?>
