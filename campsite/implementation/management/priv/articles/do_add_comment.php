<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_forum.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_user.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_comment_subject = Input::Get('f_comment_subject');
$f_comment_body = Input::Get('f_comment_body');

// Check that the article exists.
$articleObj =& new Article($f_language_id, $f_article_number);
if (!$articleObj->exists()) {
    exit;
}

// Get the publication.
$publicationObj =& new Publication($articleObj->getPublicationId());
$forumId = $publicationObj->getForumId();

// Create the forum if it doesnt exist.
if (!$forumId) {
    $forum =& new Phorum_forum();
} else {
    $forum =& new Phorum_forum($forumId);
}
if (!$forum->exists()) {
    $forum->create();
    $publicationObj->setForumId($forum->getForumId());
}

// Check if this article already has a thread
$threadId = ArticleComment::GetCommentThreadId($f_article_number, $f_language_id);
if (!$threadId) {
    $threadId = 0;
}

// Add the user if he doesnt exist in the Phorum user table
$phorumUser =& new Phorum_user($User->getUserId());
if (!$phorumUser->exists()) {
    $success = $phorumUser->create($User->getUserName(),
                                   $User->getEmail(),
                                   $User->getUserId());
}

if ($phorumUser->exists()) {
    // Create the comment
    $commentObj =& new Phorum_message();
    $commentObj->create($forumId,
                        $f_comment_subject,
                        $f_comment_body,
                        $threadId,
                        $threadId,
                        $User->getRealName(),
                        $User->getEmail(),
                        $User->getUserId());

   // Link the thread to the article if it is the first post
   if ($threadId == 0) {
       ArticleComment::Link($f_article_number, $f_language_id, $commentObj->getMessageId());
   }
}

header("Location: ".camp_html_article_url($articleObj, $f_language_selected, "edit.php"));
exit;
?>