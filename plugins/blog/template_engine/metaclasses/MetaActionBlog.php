<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');

define('ACTION_BLOG_ERR_NO_TITLE', 'ACTION_BLOG_ERR_NO_TITLE');
define('ACTION_BLOG_ERR_NO_CONTENT', 'ACTION_BLOG_ERR_NO_CONTENT');
define('ACTION_BLOG_ERR_INVALID_BLOG', 'ACTION_BLOG_ERR_INVALID_BLOG');
define('ACTION_BLOG_ERR_NO_USER', 'ACTION_BLOG_ERR_NO_USER');
define('ACTION_BLOG_ERR_NO_PERMISSION', 'ACTION_BLOG_ERR_NO_PERMISSION');


class MetaActionBlog extends MetaAction
{
    private $m_blog;
    
    /**
     * Reads the input parameters and sets up the blog action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_name = 'blog';
        $this->m_defined = true;

        if (isset($p_input['f_blog_id'])) {
            $this->m_properties['blog_id'] = $p_input['f_blog_id'];
        }
        
        if (isset($p_input['f_blog_title'])) {
            $this->m_properties['title'] = $p_input['f_blog_title'];
        }
        
        if (isset($p_input['f_blog_info'])) {
            $this->m_properties['info'] = $p_input['f_blog_info'];
        }
        
        if (isset($p_input['f_blog_request_text'])) {
            $this->m_properties['request_text'] = $p_input['f_blog_request_text'];
        }
        
        if (isset($p_input['f_blog_status'])) {
            $this->m_properties['status'] = $p_input['f_blog_status'];
        }
        
        $this->m_blog = new Blog($p_input['f_blog_id']);
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {         
        if (!$p_context->user->defined) {
            $this->m_error = new PEAR_Error('You must login to edit blogs.', ACTION_BLOG_ERR_NO_USER);
            return false;   
        }
               
        if ($this->m_blog->exists()) {
            
            // to edit existing blog, check privileges
            if ($this->m_blog->getProperty('fk_user_id') != $p_context->user->identifier && !$p_context->user->has_property('plugin_blog_moderator')) {
                $this->m_error = new PEAR_Error('You are not allowed to edit this blog.', ACTION_BLOG_ERR_NO_PERMISSION);      
                return false;
            }
                
            if (isset($this->m_properties['title']) && !strlen($this->m_properties['title'])) {
                $this->m_error = new PEAR_Error('No entry title was given.', ACTION_BLOG_ERR_NO_TITLE);
                return false;
            }
            
            if (isset($this->m_properties['info']) && !strlen($this->m_properties['info'])) {
                $this->m_error = new PEAR_Error('No info text was given.', ACTION_BLOG_ERR_NO_INFO);
                return false;
            }
            
            if (isset($this->m_properties['title'])) {
                $this->m_blog->setProperty('title', $this->m_properties['title']);
            }
            
            if (isset($this->m_properties['info'])) {
                $this->m_blog->setProperty('info', $this->m_properties['info']);
            }
            
            if (isset($this->m_properties['request_text'])) {
                $this->m_blog->setProperty('request_text', $this->m_properties['request_text']);
            }
                    
            if (isset($this->m_properties['status'])) {
                $this->m_blog->setProperty('status', $this->m_properties['status']);
            }
            
            $this->m_error = ACTION_OK;
            return true;

        } else {
            // create new blog

            if (!strlen($this->m_properties['title'])) {
                $this->m_error = new PEAR_Error('No entry title was given.', ACTION_BLOG_ERR_NO_TITLE);
                return false;
            }
            
            if (!strlen($this->m_properties['info'])) {
                $this->m_error = new PEAR_Error('No info text was given.', ACTION_BLOG_ERR_NO_INFO);
                return false;
            }
            
             if (!strlen($this->m_properties['request_text'])) {
                $this->m_error = new PEAR_Error('No request text was given.', ACTION_BLOG_ERR_NO_REQUEST_TEXT);
                return false;
            }
            
            if ($this->m_blog->create($p_context->user->identifier,
                                      $p_context->language->number,
                                      $this->m_properties['title'], 
                                      $this->m_properties['info'],
                                      $this->m_properties['request_text'])) {
                $_REQUEST['f_blog_id'] = $this->m_blog->identifier;
                $this->m_error = ACTION_OK;
                return true;   
            }   
            
        }
        return false;
    }
}

?>