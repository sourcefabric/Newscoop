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
require_once($_SERVER['DOCUMENT_ROOT'].'/include/captcha/php-captcha.inc.php');

function camp_submit_comment($p_env_vars, $p_parameters, $p_cookies)
{
	global $g_ado_db;

	// Read mandatory fields.
	$f_language_id = $p_parameters['IdLanguage'];
	$f_article_number = $p_parameters['NrArticle'];
	$f_comment_subject = $p_parameters['CommentSubject'];
	$f_comment_body = $p_parameters['CommentContent'];

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

	// Detect where to read from the user identification parameters:
	// - cookies (default)
	// - GET/POST parameters
	$userInfo = array();
	if (isset($p_cookies['LoginUserId']) && isset($p_cookies['LoginUserKey'])
			&& $p_cookies['LoginUserId'] != '') {
		$userInfo =& $p_cookies;
	} elseif (isset($p_parameters['LoginUserId']) && isset($p_parameters['LoginUserKey'])) {
		$userInfo =& $p_parameters;
	}

	// If user identification parameters were set initialize the user object.
	// If not, set $user variable to null.
	$user = null;
	if (isset($userInfo['LoginUserId']) && isset($userInfo['LoginUserKey'])
		&& is_numeric($userInfo['LoginUserId']) && is_numeric($userInfo['LoginUserKey'])) {
		// Check if user exists in the table.
		$queryStr = "SELECT * FROM Users WHERE Id='".$userInfo['LoginUserId']."'";
		$row = $g_ado_db->GetRow($queryStr);
		if ($row && $row['KeyId'] == $userInfo['LoginUserKey']) {
			$user =& new User($userInfo['LoginUserId']);
			if ($user->exists()) {
				$userId = $user->getUserId();
				$userEmail = $user->getEmail();
				$userRealName = $user->getRealName();

				$phorumUser =& new Phorum_user($userId);
				// Check if the phorum user existed or was created successfuly.
				// If not, set the error code to 'internal error' and exit.
				if (!$phorumUser->exists()
						&& !$phorumUser->create($user->getUserName(), $userEmail, $userId)) {
					$p_parameters["ArticleCommentSubmitResult"] = 5000;
					camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
					exit;
				}
			} else {
				$user = null;
			}
		}
	}

	// When user unset check if public is allowed to post comments.
	if (is_null($user)) {
		if ($forum->getPublicPermissions() & (PHORUM_USER_ALLOW_NEW_TOPIC | PHORUM_USER_ALLOW_REPLY)) {
			$userId = null;
			$userEmail = $p_parameters['CommentReaderEMail'];
			if (trim($userEmail) == '') {
				$p_parameters["ArticleCommentSubmitResult"] = 5007;
				camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
				exit;
			}
			$userRealName = $userEmail;
		} else {
			$p_parameters["ArticleCommentSubmitResult"] = 5001;
			camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
			exit;
		}
	}

	// Validate the CAPTCHA code if it was enabled for the current publication.
	if ($publicationObj->isCaptchaEnabled()) {
		session_start();
		$f_captcha_code = $p_parameters['f_captcha_code'];
		if (trim($f_captcha_code) == '') {
			$p_parameters["ArticleCommentSubmitResult"] = 5008;
			camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
			exit;
		}
		if (!PhpCaptcha::Validate($f_captcha_code, true)) {
			$p_parameters["ArticleCommentSubmitResult"] = 5009;
			camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
			exit;
		}
	}

	// Check if the comment content was filled in.
	if (trim($f_comment_body) == '') {
		$p_parameters["ArticleCommentSubmitResult"] = 5002;
		camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
		exit;
	}

	// Check if the reader was banned from posting comments.
	if (Phorum_user::IsBanned($userRealName, $userEmail)) {
		$p_parameters["ArticleCommentSubmitResult"] = 5005;
		camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
		exit;
	}

	// Create the first post message (if needed)
	$articleObj =& new Article($f_language_id, $f_article_number);
	$firstPost = camp_comment_first_post($articleObj, $forumId);
	$threadId = $firstPost->getThreadId();

	// Set the parent to the currently viewed comment if a certain existing
	// comment was selected. Otherwise, set the parent identifier to the root message.
	if (isset($p_parameters['acid']) && $p_parameters['acid'] > 0) {
		$parentId = 0 + $p_parameters['acid'];
	} else {
		$parentId = $firstPost->getMessageId();
	}

	// Create the comment. If there was an error creating the comment set the
	// error code to 'internal error' and exit.
	$commentObj =& new Phorum_message();
	if (!$commentObj->create($forumId,
							$f_comment_subject,
							$f_comment_body,
							$threadId,
							$parentId,
							$userRealName,
							$userEmail,
							is_null($userId) ? 0 : $userId)) {
		$p_parameters["ArticleCommentSubmitResult"] = 5000;
		camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
		exit;
	}

	// If the user was unknown (public comment) and public comments were moderated
	// or the user was known (subscriber comment) and subscriber comments were moderated
	// set the comment status to 'hold'. Otherwise, set the status to 'approved'.
	if ((!is_null($userId) && $publicationObj->commentsSubscribersModerated())
			|| (is_null($userId) && $publicationObj->commentsPublicModerated())) {
		$commentObj->setStatus(PHORUM_STATUS_HOLD);
	} else {
		$commentObj->setStatus(PHORUM_STATUS_APPROVED);
	}

	// Link the message to the current article.
	$isFirstMessage = ($threadId == 0);
	ArticleComment::Link($f_article_number, $f_language_id, $commentObj->getMessageId(), $isFirstMessage);

	$p_parameters["ArticleCommentSubmitResult"] = 0;
	unset($p_parameters["CommentReaderEMail"]);
	unset($p_parameters["CommentSubject"]);
	unset($p_parameters["CommentContent"]);
	camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies);
}

/**
 * Create the first message for an article, which is a blank message
 * with the title of the article as the subject.
 *
 * @param Article $p_article
 * @param int $p_forumId
 * @return mixed
 * 		The comment created (or the one that already exists) on success,
 *  	or false on error.
 */
function camp_comment_first_post($p_article, $p_forumId)
{
	// Check if the first post already exists.
	$articleNumber = $p_article->getArticleNumber();
	$languageId = $p_article->getLanguageId();
	$firstPost = ArticleComment::GetCommentThreadId($articleNumber, $languageId);
	if ($firstPost) {
		return new Phorum_message($firstPost);
	}

	// Get article creator
	$user =& new User($p_article->getCreatorId());
	$userId = $user->getUserId();
	$userEmail = $user->getEmail();
	$userRealName = $user->getRealName();

	// Create phorum user if necessary
	$phorumUser =& new Phorum_user($userId);
	if (!$phorumUser->exists()
		&& !$phorumUser->create($user->getUserName(), $userEmail, $userId)) {
		return false;
	}

	// Create the comment.
	$title = $p_article->getTitle();
	$commentObj =& new Phorum_message();
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