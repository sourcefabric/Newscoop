<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');

define('ACTION_BLOGCOMMENT_ERR_NO_TITLE', 'ACTION_BLOGCOMMENT_ERR_NO_TITLE');
define('ACTION_BLOGCOMMENT_ERR_NO_CONTENT', 'ACTION_BLOGCOMMENT_ERR_NO_CONTENT');
define('ACTION_BLOGCOMMENT_ERR_INVALID_ENTRY', 'ACTION_BLOGCOMMENT_ERR_INVALID_ENTRY');


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
        
        if (!isset($p_input['f_blogcomment_title']) || empty($p_input['f_blogcomment_title'])) {
            $this->m_error = new PEAR_Error('The comment subject was not filled in.', ACTION_PREVIEW_BLOGCOMMENT_ERR_NO_TITLE);
            return;
        }
        if (!isset($p_input['f_blogcomment_content']) || empty($p_input['f_blogcomment_content'])) {
            $this->m_error = new PEAR_Error('The comment content was not filled in.', ACTION_PREVIEW_BLOGCOMMENT_ERR_NO_CONTENT);
            return;
        }
        if (SystemPref::Get('PLUGIN_BLOGCOMMENT_USE_CAPTCHA') == 'Y') {
            session_start();
            $f_captcha_code = $p_input['f_captcha_code'];
            if (is_null($f_captcha_code) || empty($f_captcha_code)) {
                $this->m_error = new PEAR_Error('Please enter the code shown in the image.', ACTION_SUBMIT_BLOGCOMMENT_ERR_NO_CAPTCHA_CODE);
                return false;
            }
            if (!PhpCaptcha::Validate($f_captcha_code, true)) {
                $this->m_error = new PEAR_Error('The code you entered is not the same with the one shown in the image.', ACTION_SUBMIT_BLOGCOMMENT_ERR_INVALID_CAPTCHA_CODE);
                return false;
            }            
        }
        
        $this->m_properties['title'] = $p_input['f_blogcomment_title'];
        $this->m_properties['content'] = $p_input['f_blogcomment_content'];
        $this->m_properties['mood'] = $p_input['f_blogcomment_mood'];
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
                        $this->m_error = new PEAR_Error('Name was empty.', ACTION_BLOGCOMMENT_ERR_INVALID_USER);    
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
                    $this->m_error = new PEAR_Error('Only registered users can post comments.', ACTION_BLOGCOMMENT_ERR_INVALID_USER);
                    return false;     
                break;
            }
        }
               
        if ($this->m_blogcomment->exists()) {
            /*
            // to edit existing blogcomment, check privileges 
            $MetaInterview = new MetaInterview($this->m_blogcomment->getProperty('fk_interview_id'));
            
            $is_admin = $MetaInterview->isUserAdmin($p_context);
            $is_moderator = $MetaInterview->isUserModerator($p_context);
            $is_guest = $MetaInterview->isUserGuest($p_context);
            
            if (!$is_admin && !$is_moderator && !$is_guest) {
                return false;    
            }
            
            if ($is_guest) {
                # have to answer, change status automatically
                if (!strlen($this->m_properties['answer'])) {
                    $this->m_error = new PEAR_Error('An answer was not given.', ACTION_INTERVIEWITEM_ERR_NO_ANSWER);
                    return false;
                }
                $this->m_blogcomment->setProperty('answer', $this->m_properties['answer']);
                $this->m_blogcomment->setProperty('status', 'published');
            }
            
            if ($is_moderator) {
                if (isset($this->m_properties['question'])) {    
                    $this->m_blogcomment->setProperty('question', $this->m_properties['question']);
                }
                
                if (isset($this->m_properties['answer'])) {    
                    $this->m_blogcomment->setProperty('answer', $this->m_properties['answer']);
                }    
    
                if (isset($this->m_properties['status']) && ($is_admin || $is_moderator)) {    
                    $this->m_blogcomment->setProperty('status', $this->m_properties['status']);
                }  
            }
            
            if ($is_admin) {
                if (isset($this->m_properties['question'])) {    
                    $this->m_blogcomment->setProperty('question', $this->m_properties['question']);
                }
                
                if (isset($this->m_properties['answer'])) {    
                    $this->m_blogcomment->setProperty('answer', $this->m_properties['answer']);
                }    
    
                if (isset($this->m_properties['status']) && ($is_admin || $is_moderator)) {    
                    $this->m_blogcomment->setProperty('status', $this->m_properties['status']);
                }
            }
            
            $this->m_error = ACTION_OK;
            return true;
            */
        } else {
            // create new blogcomment
            
            $BlogEntry = new BlogEntry($this->m_properties['blogentry_id']);
            
            if ($this->m_blogcomment->create($BlogEntry->getId(), 
                                             is_object($p_context->user) && $p_context->user->identifier ? $p_context->user->identifier : 0,
                                             $this->m_properties['user_name'],
                                             $this->m_properties['user_email'],
                                             $this->m_properties['title'], 
                                             $this->m_properties['content'], 
                                             $this->m_properties['mood'])) {
                $this->m_error = ACTION_OK;
                return true;   
            }   
            
        }
        return false;
    }
}

?>