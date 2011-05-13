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

require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');
require_once($GLOBALS['g_campsiteDir'].'/include/get_ip.php');

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
        $this->m_properties['rejected'] = null;

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
        $this->m_properties['nickname'] = isset($p_input['f_comment_nickname']) ?
                                          $p_input['f_comment_nickname'] : 'anonymous';
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
     * @return void
     */
    private function _processCaptcha()
    {
        //@session_start();
        $captchaHandler = CampRequest::GetVar('f_captcha_handler', '', 'POST');
        if (!empty($captchaHandler)) {
            $captcha = Captcha::factory($captchaHandler);
            if (!$captcha->validate()) {
                $this->m_error = new PEAR_Error('The code you entered is not the same as the one shown.',
                ACTION_SUBMIT_COMMENT_ERR_INVALID_CAPTCHA_CODE);
                return FALSE;
            }
        } else {
            $f_captcha_code = CampRequest::GetVar('f_captcha_code');
            if (is_null($f_captcha_code) || empty($f_captcha_code)) {
                $this->m_error = new PEAR_Error('Please enter the code shown in the image.',
                ACTION_SUBMIT_COMMENT_ERR_NO_CAPTCHA_CODE);
                return FALSE;
            }
            if (!PhpCaptcha::Validate($f_captcha_code, true)) {
                $this->m_error = new PEAR_Error('The code you entered is not the same with the one shown in the image.',
                ACTION_SUBMIT_COMMENT_ERR_INVALID_CAPTCHA_CODE);
                return FALSE;
            }
        }
        return TRUE;
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


        $publication_id =  $articleMetaObj->publication->identifier;

        // Get the publication.
        $publicationObj = new Publication($publication_id);
        $user = $p_context->user;
        $userIp = getIp();
        if ($user->defined)
        {
            $userId = $user->identifier;
            $userEmail = $user->email;
            $userRealName = $user->name;
        }
        else
        {
            if(!$publicationObj->getPublicComments())
            {
                $this->m_error = new PEAR_Error('You must be a registered user in order to submit a comment. Please subscribe or log in if you already have a subscription.',
                ACTION_SUBMIT_COMMENT_ERR_NO_PUBLIC);
                return false;
            }
            else
            {
                if (!isset($this->m_properties['reader_email'])) {
                    $this->m_error = new PEAR_Error('EMail field is empty. You must fill in your EMail address.',
                    ACTION_SUBMIT_COMMENT_ERR_NO_EMAIL);
                    return false;
                }
            }
            $userId = null;
            $userEmail = $this->m_properties['reader_email'];
            $userRealName = $this->m_properties['nickname'];
        }

        // Validate the CAPTCHA code if it was enabled for the current publication.
        if ($publicationObj->isCaptchaEnabled()) {
            if ($this->_processCaptcha() === FALSE) {
                return FALSE;
            }
        }
        // Check if the reader was banned from posting comments.
        global $controller;
        $repositoryAcceptance = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment\Acceptance');
        $repository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
        if ($repositoryAcceptance->checkParamsBanned($userRealName, $userEmail, $userIp, $publication_id))
        {
            $this->m_error = new PEAR_Error('You are banned from submitting comments.',
            ACTION_SUBMIT_COMMENT_ERR_BANNED);
            return false;
        }
        // get the article object
        $articleObj = new Article($articleMetaObj->language->number, $articleMetaObj->number);

        // Set the parent to the currently viewed comment if a certain existing
        // comment was selected. Otherwise, set the parent identifier to the root message.

        // Create the comment. If there was an error creating the comment set the
        // error code to 'internal error' and exit.
        $values = array(
            'thread' => $articleMetaObj->number,
            'language' => $articleMetaObj->language->number,
            'name' => $userRealName,
            'email'=> $userEmail,
            'message' =>  $this->m_properties['content'],
            'subject' => $this->m_properties['subject'],
            'ip' => $userIp,
            'time_created' => new DateTime
        );

        // If the user was unknown (public comment) and public comments were moderated
        // or the user was known (subscriber comment) and subscriber comments were moderated
        // set the comment status to 'hold'. Otherwise, set the status to 'approved'.
        if ((!is_null($userId) && $publicationObj->commentsSubscribersModerated())
        || (is_null($userId) && $publicationObj->commentsPublicModerated())) {
            $values['status'] = "pending";
        } else {
            $values['status'] = "approved";
        }

        // If the user was known set it
        if(!is_null($userId))
            $values['user'] = $userId;

        //If there is a comment idetifier set it the parent of the comment
        if($p_context->comment->identifier)
            $values['parent'] = $p_context->comment->identifier;

        $commentObj = $repository->getPrototype();
        $comment = $repository->save($commentObj,$values);
        $repository->flush();
        if (!$comment) {
            $this->m_error = new PEAR_Error('There was an internal error when submitting the comment (code 3).',
            ACTION_SUBMIT_COMMENT_ERR_INTERNAL);
            return false;
        }

        $controller->getHelper('actionStack')->actionToStack("moderate-comment","notification","admin", array('comment'=>$comment->getId()));

        $p_context->comment = new MetaComment($comment->getId());
        $p_context->comment = new MetaComment($comment->getId());
        $p_context->default_url->reset_parameter('f_comment_reader_email');
        $p_context->default_url->reset_parameter('f_comment_subject');
        $p_context->default_url->reset_parameter('f_comment_content');
        $p_context->default_url->reset_parameter('f_submit_comment');
        $p_context->default_url->reset_parameter('f_captcha_code');
        $p_context->url->reset_parameter('f_comment_reader_email');
        $p_context->url->reset_parameter('f_comment_subject');
        $p_context->url->reset_parameter('f_comment_content');
        $p_context->url->reset_parameter('f_submit_comment');
        $p_context->url->reset_parameter('f_captcha_code');

        $this->m_properties['rejected'] = false;

        $this->m_error = ACTION_OK;
        return true;
    }

}