<?php

define('ACTION_PREVIEW_COMMENT_ERR_NO_SUBJECT', 'action_comment_preview_err_no_subject');
define('ACTION_PREVIEW_COMMENT_ERR_NO_CONTENT', 'action_comment_preview_err_no_content');
define('ACTION_PREVIEW_COMMENT_ERR_NO_ARTICLE', 'action_comment_preview_err_no_article');
define('ACTION_PREVIEW_COMMENT_ERR_NOT_ENABLED', 'action_comment_preview_err_not_enabled');
define('ACTION_PREVIEW_COMMENT_ERR_NO_EMAIL', 'action_comment_preview_err_no_email');
define('ACTION_PREVIEW_COMMENT_ERR_NO_PUBLIC', 'action_comment_preview_err_no_public');
define('ACTION_PREVIEW_COMMENT_ERR_BANNED', 'action_comment_preview_err_banned');


class MetaActionPreview_Comment extends MetaAction
{
    /**
     * Reads the input parameters and sets up the comment preview action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'preview_comment';
        $this->m_error = null;

        if (!isset($p_input['f_comment_subject']) || empty($p_input['f_comment_subject'])) {
            $this->m_error = new PEAR_Error('The comment subject was not filled in.',
            ACTION_PREVIEW_COMMENT_ERR_NO_SUBJECT);
            return;
        }
        if (!isset($p_input['f_comment_content']) || empty($p_input['f_comment_content'])) {
            $this->m_error = new PEAR_Error('The comment content was not filled in.',
            ACTION_PREVIEW_COMMENT_ERR_NO_CONTENT);
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
            ACTION_PREVIEW_COMMENT_ERR_NO_ARTICLE);
            return false;
        }
        if (!$articleMetaObj->comments_enabled || $articleMetaObj->comments_locked)  {
            $this->m_error = new PEAR_Error('Comments are not enabled for this publication/article.',
            ACTION_PREVIEW_COMMENT_ERR_NOT_ENABLED);
            return false;
        }

        // Get the publication.
        $publicationObj = new Publication($articleMetaObj->publication->identifier);

        $user = $p_context->user;
        if ($user->defined) {
            $this->m_properties['reader_email'] = $user->email;
        } else {
            if (!isset($this->m_properties['reader_email']))
            {
                $this->m_error = new PEAR_Error('You must be a registered user in order to submit a comment. Please subscribe or log in if you already have a subscription.',
                ACTION_SUBMIT_COMMENT_ERR_NO_PUBLIC);
                return false;
            }
            if(!$publicationObj->getPublicComments())
            {
                $this->m_error = new PEAR_Error('EMail field is empty. You must fill in your EMail address.',
                ACTION_SUBMIT_COMMENT_ERR_NO_EMAIL);
                return false;
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

        $this->m_error = ACTION_OK;
        return true;
    }
}

?>