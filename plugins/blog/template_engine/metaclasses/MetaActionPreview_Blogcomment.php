<?php

if (!defined('ACTION_BLOGCOMMENT_ERR_INVALID_ENTRY')) {
	define('ACTION_BLOGCOMMENT_ERR_INVALID_ENTRY', 'ACTION_BLOGCOMMENT_ERR_INVALID_ENTRY');
	define('ACTION_BLOGCOMMENT_ERR_NO_TITLE', 'ACTION_BLOGCOMMENT_ERR_NO_TITLE');
	define('ACTION_BLOGCOMMENT_ERR_NO_CONTENT', 'ACTION_BLOGCOMMENT_ERR_NO_CONTENT');
	define('ACTION_BLOGCOMMENT_ERR_NO_NAME', 'ACTION_BLOGCOMMENT_ERR_NO_NAME');
	define('ACTION_BLOGCOMMENT_ERR_NO_EMAIL', 'ACTION_BLOGCOMMENT_ERR_NO_EMAIL');
	define('ACTION_BLOGCOMMENT_ERR_NOT_REGISTERED', 'ACTION_BLOGCOMMENT_ERR_NOT_REGISTERED');
}

class MetaActionPreview_Blogcomment extends MetaAction
{
    /**
     * Reads the input parameters and sets up the comment preview action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'preview_blogcomment';
        $this->m_error = null;

        $BlogEntry = new BlogEntry($p_input['f_blogentry_id']); 
          
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
        
        $this->m_properties['title'] = $p_input['f_blogcomment_title'];
        $this->m_properties['content'] = $p_input['f_blogcomment_content'];
        $this->m_properties['mood'] = new MetaTopic($p_input['f_blogcomment_mood_id']);
        $this->m_properties['user_name'] = $p_input['f_blogcomment_user_name'];
        $this->m_properties['user_email'] = $p_input['f_blogcomment_user_email'];
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
            $this->m_properties['user_name'] = $p_context->user->name;
            $this->m_properties['user_email'] = $p_context->user->email;
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
        
        $this->m_error = ACTION_OK;
        return true;
    }
}

?>