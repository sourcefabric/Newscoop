<?php

if (!defined('ACTION_BLOGCOMMENT_ERR_INVALID_ENTRY')) {
	define('ACTION_BLOGCOMMENT_ERR_INVALID_ENTRY', 'ACTION_BLOGCOMMENT_ERR_INVALID_ENTRY');
	define('ACTION_BLOGCOMMENT_ERR_NO_TITLE', 'ACTION_BLOGCOMMENT_ERR_NO_TITLE');
	define('ACTION_BLOGCOMMENT_ERR_NO_CONTENT', 'ACTION_BLOGCOMMENT_ERR_NO_CONTENT');
	define('ACTION_BLOGCOMMENT_ERR_NO_NAME', 'ACTION_BLOGCOMMENT_ERR_NO_NAME');
	define('ACTION_BLOGCOMMENT_ERR_NO_EMAIL', 'ACTION_BLOGCOMMENT_ERR_NO_EMAIL');
	define('ACTION_BLOGCOMMENT_ERR_NOT_REGISTERED', 'ACTION_BLOGCOMMENT_ERR_NOT_REGISTERED');
	define('ACTION_BLOGCOMMENT_ERR_NO_CAPTCHA_CODE', 'ACTION_BLOGCOMMENT_ERR_NO_CAPTCHA_CODE');
}

class MetaActionSubmit_Blogcomment extends MetaAction
{
    private $m_blogcomment;

    /**
     * Reads the input parameters and sets up the blogcomment action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_name = 'submit_blogcomment';
        $this->m_defined = true;


        $this->m_properties['blogentry_id'] = $p_input['f_blogentry_id'];
        $BlogEntry = new BlogEntry($this->m_properties['blogentry_id']);

        if (!$BlogEntry->exists()) {
            $this->m_error = new PEAR_Error('None or invalid blogentry was given.', ACTION_BLOGCOMMENT_ERR_INVALID_ENTRY);
            return;
        }
        /*
        if (!isset($p_input['f_blogcomment_title']) || empty($p_input['f_blogcomment_title'])) {
            $this->m_error = new PEAR_Error('The comment subject was not filled in.', ACTION_BLOGCOMMENT_ERR_NO_TITLE);
            return;
        }
        */
        if (!isset($p_input['f_blogcomment_content']) || empty($p_input['f_blogcomment_content'])) {
            $this->m_error = new PEAR_Error('The comment content was not filled in.', ACTION_BLOGCOMMENT_ERR_NO_CONTENT);
            return;
        }
        if (SystemPref::Get('PLUGIN_BLOGCOMMENT_USE_CAPTCHA') == 'Y') {
            @session_start();
            $f_captcha_code = $p_input['f_captcha_code'];
            if (is_null($f_captcha_code) || empty($f_captcha_code)) {
                $this->m_error = new PEAR_Error('Please enter the code shown in the image.', ACTION_BLOGCOMMENT_ERR_NO_CAPTCHA_CODE);
                return false;
            }
            if (!PhpCaptcha::Validate($f_captcha_code, true)) {
                $this->m_error = new PEAR_Error('The code you entered is not the same with the one shown in the image.', ACTION_BLOGCOMMENT_ERR_INVALID_CAPTCHA_CODE);
                return false;
            }
        }

        $this->m_properties['title'] = $p_input['f_blogcomment_title'];
        $this->m_properties['content'] = $p_input['f_blogcomment_content'];
        $this->m_properties['mood_id'] = $p_input['f_blogcomment_mood_id'];
        $this->m_properties['user_name'] = $p_input['f_blogcomment_user_name'];
        $this->m_properties['user_email'] = $p_input['f_blogcomment_user_email'];

        $this->m_blogcomment = new BlogComment($p_input['f_blogcomment_id']);
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

        $user = $p_context->user;

        if ($user->defined) {
            $this->m_properties['user_name'] = '';
            $this->m_properties['user_email'] = '';
        } else {
            switch(SystemPref::Get('PLUGIN_BLOGCOMMENT_MODE')) {
                case 'name':
                    if (!strlen($this->m_properties['user_name'])) {
                        $this->m_error = new PEAR_Error('Name was empty.', ACTION_BLOGCOMMENT_ERR_NO_NAME);
                        return false;
                    }
                break;

                case 'email':
                    if (!strlen($this->m_properties['user_name'])) {
                        $this->m_error = new PEAR_Error('Name was empty.', ACTION_BLOGCOMMENT_ERR_NO_NAME);
                        return false;
                    }
                    if (!CampMail::ValidateAddress($this->m_properties['user_email'])) {
                        $this->m_error = new PEAR_Error('Email was empty or invalid.', ACTION_BLOGCOMMENT_ERR_NO_EMAIL);
                        return false;
                    }
                break;

                case 'registered':
                default:
                    $this->m_error = new PEAR_Error('Only registered users can post comments.', ACTION_BLOGCOMMENT_ERR_NOT_REGISTERED);
                    return false;
                break;
            }
        }

        $BlogEntry = new BlogEntry($this->m_properties['blogentry_id']);

        if ($this->m_blogcomment->create($BlogEntry->getId(),
                                         is_object($p_context->user) && $p_context->user->identifier ? $p_context->user->identifier : 0,
                                         $this->m_properties['user_name'],
                                         $this->m_properties['user_email'],
                                         $this->m_properties['title'],
                                         $this->m_properties['content'],
                                         $this->m_properties['mood_id'])) {
            $this->m_error = ACTION_OK;
            return true;
        }

        return false;
    }
}

?>