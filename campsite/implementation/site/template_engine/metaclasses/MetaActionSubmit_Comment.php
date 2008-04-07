<?php

define('ACTION_SUBMIT_COMMENT_ERR_INTERNAL', 'action_comment_submit_err_internal');
define('ACTION_SUBMIT_COMMENT_ERR_NO_SUBJECT', 'action_comment_submit_err_no_subject');
define('ACTION_SUBMIT_COMMENT_ERR_NO_CONTENT', 'action_comment_submit_err_no_content');
define('ACTION_SUBMIT_COMMENT_ERR_NO_ARTICLE', 'action_comment_submit_err_no_article');
define('ACTION_SUBMIT_COMMENT_ERR_NOT_ENABLED', 'action_comment_submit_err_not_enabled');
define('ACTION_SUBMIT_COMMENT_ERR_NO_EMAIL', 'action_comment_submit_err_no_email');
define('ACTION_SUBMIT_COMMENT_ERR_NO_PUBLIC', 'action_comment_submit_err_no_public');
define('ACTION_SUBMIT_COMMENT_ERR_NO_CAPTCHA_CODE', 'action_comment_submit_err_no_captcha_code');
define('ACTION_SUBMIT_COMMENT_ERR_INVALID_CAPTCHA_CODE', 'action_comment_submit_err_invalid_captcha_code');
define('ACTION_SUBMIT_COMMENT_ERR_BANNED', 'action_comment_submit_err_banned');
define('ACTION_SUBMIT_COMMENT_ERR_REJECTED', 'action_comment_submit_err_rejected');

require_once($_SERVER['DOCUMENT_ROOT'].'/include/captcha/php-captcha.inc.php');


class MetaActionSubmit_Comment extends MetaAction
{
    /**
     * Reads the input parameters and sets up the comment submit action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'submit_comment';
        $this->m_error = null;

        if (!isset($p_input['f_comment_subject']) || empty($p_input['f_comment_subject'])) {
            $this->m_error = new PEAR_Error('The comment subject was not filled in.',
            ACTION_SUBMIT_COMMENT_ERR_NO_SUBJECT);
            return;
        }
        if (!isset($p_input['f_comment_content']) || empty($p_input['f_comment_content'])) {
            $this->m_error = new PEAR_Error('The comment content was not filled in.',
            ACTION_SUBMIT_COMMENT_ERR_NO_CONTENT);
            return;
        }
        $this->m_properties['subject'] = $p_input['f_comment_subject'];
        $this->m_properties['content'] = $p_input['f_comment_content'];
        if (isset($p_input['f_comment_reader_email'])) {
            $readerEmail = trim($p_input['f_comment_reader_email']);
            if (!empty($readerEmail)) {
                $this->m_properties['reader_email'] = $readerEmail;
            }
        }
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {
        $p_context->default_url->reset_parameter('f_'.$this->m_name);
        $p_context->url->reset_parameter('f_'.$this->m_name);

        if (!is_null($this->m_error)) {
            return false;
        }

        // Check that the article exists.
        $articleMetaObj = $p_context->default_article;
        if (!$articleMetaObj->defined) {
            $this->m_error = new PEAR_Error('The article was not selected. You must view an article in order to post comments.',
            ACTION_SUBMIT_COMMENT_ERR_NO_ARTICLE);
            return false;
        }
        if (!$articleMetaObj->comments_enabled || $articleMetaObj->comments_locked)  {
            $this->m_error = new PEAR_Error('Comments are not enabled for this publication/article.',
            ACTION_SUBMIT_COMMENT_ERR_NOT_ENABLED);
            return false;
        }

        // Get the publication.
        $publicationObj = new Publication($articleMetaObj->publication->identifier);
        $forum = new Phorum_forum($publicationObj->getForumId());
        if (!$forum->exists()) {
            $forum->create();
            $forum->setName($publicationObj->getName());
            $publicationObj->setForumId($forum->getForumId());
        }
        $forumId = $forum->getForumId();

        $user = $p_context->user;
        if ($user->defined) {
            $phorumUser = Phorum_user::GetByUserName($user->uname);
            if (is_null($phorumUser)) {
                $phorumUser = new Phorum_user();
            }
            $userId = $user->identifier;
            $userEmail = $user->email;
            $userRealName = $user->name;
            $userPasswd = $user->password_encrypted;
            // Check if the phorum user existed or was created successfuly.
            // If not, set the error code to 'internal error' and exit.
            if (!Phorum_user::CampUserExists($userId)
            && !$phorumUser->create($user->uname, $userPasswd, $userEmail, $userId)) {
                $this->m_error = new PEAR_Error('There was an internal error when submitting the comment (code 1).',
                ACTION_SUBMIT_COMMENT_ERR_INTERNAL);
                return false;
            }
        } else {
            if ($forum->getPublicPermissions() & (PHORUM_USER_ALLOW_NEW_TOPIC | PHORUM_USER_ALLOW_REPLY)) {
                if (!isset($this->m_properties['reader_email'])) {
                    $this->m_error = new PEAR_Error('EMail field is empty. You must fill in your EMail address.',
                    ACTION_SUBMIT_COMMENT_ERR_NO_EMAIL);
                    return false;
                }
                $userId = null;
                $userEmail = $this->m_properties['reader_email'];
                $userRealName = $userEmail;
            } else {
                $this->m_error = new PEAR_Error('You must be a registered user in order to submit a comment. Please subscribe or log in if you already have a subscription.',
                ACTION_SUBMIT_COMMENT_ERR_NO_PUBLIC);
                return false;
            }
        }

        // Validate the CAPTCHA code if it was enabled for the current publication.
        if ($publicationObj->isCaptchaEnabled()) {
            session_start();
            $f_captcha_code = CampRequest::GetVar('f_captcha_code');
            if (is_null($f_captcha_code) || empty($f_captcha_code)) {
                $this->m_error = new PEAR_Error('Please enter the code shown in the image.',
                ACTION_SUBMIT_COMMENT_ERR_NO_CAPTCHA_CODE);
                return false;
            }
            if (!PhpCaptcha::Validate($f_captcha_code, true)) {
                $this->m_error = new PEAR_Error('The code you entered is not the same with the one shown in the image.',
                ACTION_SUBMIT_COMMENT_ERR_INVALID_CAPTCHA_CODE);
                return false;
            }
        }

        // Check if the reader was banned from posting comments.
        if (Phorum_user::IsBanned($userRealName, $userEmail)) {
            $this->m_error = new PEAR_Error('You are banned from submitting comments.',
            ACTION_SUBMIT_COMMENT_ERR_BANNED);
            return false;
        }

        // Create the first post message (if needed)
        $articleObj = new Article($articleMetaObj->language->number, $articleMetaObj->number);
        $firstPost = $this->CreateFirstComment($articleObj, $forumId);
        if (is_null($firstPost)) {
            $this->m_error = new PEAR_Error('There was an internal error when submitting the comment (code 2).',
            ACTION_SUBMIT_COMMENT_ERR_INTERNAL);
            return false;
        }

        // Set the parent to the currently viewed comment if a certain existing
        // comment was selected. Otherwise, set the parent identifier to the root message.
        $parentMessage = new Phorum_message($p_context->comment->identifier);
        if (!$parentMessage->exists()) {
            $parentMessage = $firstPost;
        }

        // Create the comment. If there was an error creating the comment set the
        // error code to 'internal error' and exit.
        $commentObj = new Phorum_message();
        if (!$commentObj->create($forumId, $this->m_properties['subject'],
        $this->m_properties['content'], $firstPost->getThreadId(),
        $parentMessage->getMessageId(), $userRealName, $userEmail,
        is_null($userId) ? 0 : $userId)) {
            $this->m_error = new PEAR_Error('There was an internal error when submitting the comment (code 3).',
            ACTION_SUBMIT_COMMENT_ERR_INTERNAL);
            return false;
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
        $isFirstMessage = ($firstPost->getThreadId() == 0);
        ArticleComment::Link($articleMetaObj->number, $articleMetaObj->language->number,
        $commentObj->getMessageId(), $isFirstMessage);

        $p_context->comment = new MetaComment($commentObj->getMessageId());

        $p_context->url->reset_parameter('f_comment_reader_email');
        $p_context->url->reset_parameter('f_comment_subject');
        $p_context->url->reset_parameter('f_comment_content');
        $p_context->url->reset_parameter('f_submit_comment');
        $p_context->url->reset_parameter('f_captcha_code');
        
        $this->m_properties['rejected'] = false;

        $this->m_error = ACTION_OK;
        return true;
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
    private function CreateFirstComment($p_article, $p_forumId)
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
                return null;
            }
        } else {
            $userId = null;
            $userEmail = '';
            $userRealName = '';
        }

        // Create the comment.
        $title = $p_article->getTitle();
        $commentObj = new Phorum_message();
        if ($commentObj->create($p_forumId, $title, '', 0, 0, $userRealName,
        $userEmail, is_null($userId) ? 0 : $userId)) {
            // Link the message to the current article.
            ArticleComment::Link($articleNumber, $languageId, $commentObj->getMessageId(), true);
            return $commentObj;
        } else {
            return null;
        }
    } // fn camp_comment_first_post
}

?>