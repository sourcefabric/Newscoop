<?php
camp_load_translation_strings("comments");
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_forum.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_user.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

if (!$g_user->hasPermission('CommentModerate')) {
	camp_html_display_error(getGS("You do not have the right to moderate comments." ));
	exit;
}

// process all comments
foreach ($_REQUEST as $name => $value) {
    if (strstr($name, "comment_action_")) {
        $parts = split("_", $name);
        $messageId = $parts[2];
        $comment =& new Phorum_message($messageId);
        if (!$comment->exists()) {
            continue;
        }
        switch ($value) {
            case "inbox":
                $comment->setStatus(PHORUM_STATUS_HOLD);
                break;
            case "hide":
                $comment->setStatus(PHORUM_STATUS_HIDDEN);
                break;
            case "delete":
                $comment->delete();
                ArticleComment::Unlink(null, null, $messageId);
                break;
            case "approve":
                $comment->setStatus(PHORUM_STATUS_APPROVED);
                break;
        }
        $subjectStr = Input::Get('f_subject_'.$messageId, 'string', '', true);
        $comment->setSubject($subjectStr);
        $commentStr = Input::Get('f_comment_'.$messageId, 'string', '', true);
        $comment->setBody($commentStr);
    }
}
camp_html_goto_page("/$ADMIN/comments/index.php");

?>