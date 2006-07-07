<?php
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_forum.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_user.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");

$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_comment_subject = Input::Get('f_comment_subject', 'string', '', true);
$f_comment_body = Input::Get('f_comment_body');
$f_comment_parent_id = Input::Get('f_comment_id', 'int', 0, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;
}

// Check that the article exists.
$articleObj =& new Article($f_language_id, $f_article_number);
if (!$articleObj->exists()) {
    exit;
}
if (!$articleObj->commentsEnabled() || $articleObj->commentsLocked())  {
    camp_html_goto_page(camp_html_article_url($articleObj, $f_language_selected, "edit.php"));
}

// Add the user if he doesnt exist in the Phorum user table
$phorumUser =& new Phorum_user($g_user->getUserId());
if (!$phorumUser->exists()) {
    $success = $phorumUser->create($g_user->getUserName(),
                                   $g_user->getEmail(),
                                   $g_user->getUserId());
}

// Check if this article already has a thread
$threadId = ArticleComment::GetCommentThreadId($f_article_number, $f_language_id);
if (!$threadId) {
    $threadId = 0;
}

// Get the forum ID.
$publicationObj =& new Publication($articleObj->getPublicationId());
$forumId = $publicationObj->getForumId();
$forum =& new Phorum_forum($forumId);

// Exit if the forum hasnt been created (this should never happen).
if (!$forumId || !$forum->exists()) {
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_selected, "edit.php")."#add_comment");
}

// Create the comment
$commentObj =& new Phorum_message();
if ($f_comment_parent_id != 0) {
	// This is a reply
    $commentObj->create($forumId,
                        $f_comment_subject,
                        $f_comment_body,
                        $threadId,
                        $f_comment_parent_id,
                        $g_user->getRealName(),
                        $g_user->getEmail(),
                        $g_user->getUserId());
} else {
	// Either the first message or a message replying to the first message.
    $commentObj->create($forumId,
                        $f_comment_subject,
                        $f_comment_body,
                        $threadId,
                        $threadId,
                        $g_user->getRealName(),
                        $g_user->getEmail(),
                        $g_user->getUserId());
}
$commentObj->setStatus(PHORUM_STATUS_APPROVED);
// Link the message to the article
$isFirstMessage = ($threadId == 0);
ArticleComment::Link($f_article_number, $f_language_id, $commentObj->getMessageId(), $isFirstMessage);

camp_html_goto_page(camp_html_article_url($articleObj, $f_language_selected, "edit.php")."#add_comment");
?>