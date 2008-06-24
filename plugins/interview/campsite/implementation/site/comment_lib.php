<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/conf/configuration.php');
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
require_once($_SERVER['DOCUMENT_ROOT'].'/include/captcha/php-captcha.inc.php');


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
