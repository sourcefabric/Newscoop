<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_forum.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_user.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/parser_utils.php');

function camp_submit_comment($p_env_vars, $p_parameters, $p_cookies)
{
	global $g_ado_db;

	$userInfo = array();
	if (isset($p_cookies['LoginUserId']) && isset($p_cookies['LoginUserKey'])
			&& $p_cookies['LoginUserId'] != '') {
		$userInfo =& $p_cookies;
	} elseif (isset($p_parameters['LoginUserId']) && isset($p_parameters['LoginUserKey'])) {
		$userInfo =& $p_parameters;
	}

	$user = null;
	if (isset($userInfo['LoginUserId']) && isset($userInfo['LoginUserKey'])
		&& is_numeric($userInfo['LoginUserId']) && is_numeric($userInfo['LoginUserKey'])) {
		// Check if user exists in the table.
		$queryStr = "SELECT * FROM Users WHERE Id='".$userInfo['LoginUserId']."'";
		$row = $g_ado_db->GetRow($queryStr);
		if ($row && $row['KeyId'] == $userInfo['LoginUserKey']) {
			$user =& new User($userInfo['LoginUserId']);
		}
	}
	if (is_null($user) || !$user->exists()) {
		$p_parameters["ArticleCommentSubmitResult"] = 5001;
		camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
		exit;
	}

	$f_language_id = $p_parameters['IdLanguage'];
	$f_article_number = $p_parameters['NrArticle'];
	$f_comment_subject = $p_parameters['CommentSubject'];
	$f_comment_body = $p_parameters['CommentContent'];

	if (trim($f_comment_body) == '') {
		$p_parameters["ArticleCommentSubmitResult"] = 5002;
		camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
		exit;
	}

	// Check that the article exists.
	$articleObj =& new Article($f_language_id, $f_article_number);
	if (!$articleObj->exists()) {
		$p_parameters["ArticleCommentSubmitResult"] = 5003;
		camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
		exit;
	}
	if (!$articleObj->commentsEnabled() || $articleObj->commentsLocked())  {
		$p_parameters["ArticleCommentSubmitResult"] = 5004;
		camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
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
		$forum->setName($publicationObj->getName());
		$publicationObj->setForumId($forum->getForumId());
		$forumId = $forum->getForumId();
	}

	// Check if this article already has a thread
	$threadId = ArticleComment::GetCommentThreadId($f_article_number, $f_language_id);
	if (!$threadId) {
		$threadId = 0;
	}

	// Add the user if he doesnt exist in the Phorum user table
	$phorumUser =& new Phorum_user($user->getUserId());
	if (!$phorumUser->exists()) {
		$success = $phorumUser->create($user->getUserName(),
									$user->getEmail(),
									$user->getUserId());
	}

	if ($phorumUser->exists()) {
		if (Phorum_user::IsBanned($user->getRealName(), $user->getEmail())) {
			$p_parameters["ArticleCommentSubmitResult"] = 5005;
			camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
			exit;
		}
		// Create the comment
		$commentObj =& new Phorum_message();
		if (!$commentObj->create($forumId,
								$f_comment_subject,
								$f_comment_body,
								$threadId,
								$threadId,
								$user->getRealName(),
								$user->getEmail(),
								$user->getUserId())) {
			$p_parameters["ArticleCommentSubmitResult"] = 5000;
			camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
			exit;
		}
		$commentObj->setStatus(PHORUM_STATUS_APPROVED);
		// Link the message to the article
		$isFirstMessage = ($threadId == 0);
		ArticleComment::Link($f_article_number, $f_language_id, $commentObj->getMessageId(), $isFirstMessage);
	} else {
		$p_parameters["ArticleCommentSubmitResult"] = 5000;
		camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
		exit;
	}

	$p_parameters["ArticleCommentSubmitResult"] = 0;
	unset($p_parameters["CommentReaderEMail"]);
	unset($p_parameters["CommentSubject"]);
	unset($p_parameters["CommentContent"]);
	camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
}

?>