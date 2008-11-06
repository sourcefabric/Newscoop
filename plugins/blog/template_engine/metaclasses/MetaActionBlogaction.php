<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');

define('ACTION_BLOGACTION_ERR_NO_USER', 'ACTION_BLOGACTION_ERR_NO_USER');
define('ACTION_BLOGACTION_ERR_INVALID_BLOG', 'ACTION_BLOGACTION_ERR_INVALID_BLOG');
define('ACTION_BLOGACTION_ERR_INVALID_BLOGENTRY', 'ACTION_BLOGACTION_ERR_INVALID_BLOGENTRY');
define('ACTION_BLOGACTION_ERR_INVALID_BLOGCOMMENT', 'ACTION_BLOGACTION_ERR_INVALID_BLOGCOMMENT');
define('ACTION_BLOGACTION_ERR_NO_PERMISSION', 'ACTION_BLOGACTION_ERR_NO_PERMISSION');


class MetaActionBlogaction extends MetaAction
{
    private $m_blog;
    private $m_blogentry;
    private $m_blogcomment;
    
    /**
     * Reads the input parameters and sets up the blog action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_name = 'blogaction';
        $this->m_defined = true;

        $this->m_properties['action'] = $p_input['f_blogaction'];
         
        if (isset($p_input['f_blog_id'])) {
            $this->m_properties['blog_id'] = $p_input['f_blog_id'];
        }
        
        if (isset($p_input['f_blogentry_id'])) {
            $this->m_properties['blogentry_id'] = $p_input['f_blogentry_id'];
        }
        
        if (isset($p_input['f_blogcomment_id'])) {
            $this->m_properties['blogcomment_id'] = $p_input['blogcomment_id'];
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
        if (!$p_context->user->defined) {
            $this->m_error = new PEAR_Error('You must login to take blog actions.', ACTION_BLOGACTION_ERR_NO_USER);
            return false;   
        }
        
        switch ($this->m_properties['action']) {
            case 'blog_delete':
                $this->m_blog = new Blog($this->m_properties['blog_id']);
                
                if (!$this->m_blog->exists()) {
                    $this->m_error = new PEAR_Error('Invalid blog was given.', ACTION_BLOGACTION_ERR_INVALID_BLOG);
                    return false;  
                }
                
                if ($this->m_blog->getProperty('fk_user_id') != $p_context->user->identifier && !$p_context->user->has_property('plugin_blog_moderator')) {
                    $this->m_error = new PEAR_Error('You are not allowed to delete this blog.', ACTION_BLOGACTION_ERR_NO_PERMISSION);
                    return false;      
                }
                 
                $this->m_blog->delete();
                unset($_REQUEST['f_blog_id']);
                $this->m_error = ACTION_OK;
                return true;           
            break;
            
            case 'entry_delete':
                $this->m_blogentry = new BlogEntry($this->m_properties['blogentry_id']);
                
                if (!$this->m_blogentry->exists()) {
                    $this->m_error = new PEAR_Error('Invalid blogentry was given.', ACTION_BLOGACTION_ERR_INVALID_BLOGENTRY);
                    return false;  
                }
                
                if ($this->m_blogentry->getProperty('fk_user_id') != $p_context->user->identifier && !$p_context->user->has_property('plugin_blog_moderator')) {
                    $this->m_error = new PEAR_Error('You are not allowed to delete this blogentry.', ACTION_BLOGACTION_ERR_NO_PERMISSION);
                    return false;      
                }
                 
                $this->m_blogentry->delete();
                unset($_REQUEST['f_blogentry_id']);
                $this->m_error = ACTION_OK;
                return true;    
            break;
            
            case 'comment_delete':
                $this->m_blogcomment = new BlogComment($this->m_properties['blogcomment_id']);
                
                if (!$this->m_blogcomment->exists()) {
                    $this->m_error = new PEAR_Error('Invalid blogcoment was given.', ACTION_BLOGACTION_ERR_INVALID_BLOGCOMMENT);
                    return false;  
                }
                
                if ($this->m_blogcomment->getProperty('fk_user_id') != $p_context->user->identifier && !$p_context->user->has_property('plugin_blog_moderator')) {
                    $this->m_error = new PEAR_Error('You are not allowed to delete this blogentry.', ACTION_BLOGACTION_ERR_NO_PERMISSION);
                    return false;      
                }
                 
                $this->m_blogcomment->delete();
                unset($_REQUEST['f_blogcomment_id']);
                $this->m_error = ACTION_OK;
                return true;  
            break; 
        }
        
        $this->m_error = new PEAR_Error('Invalid blogaction was given.', ACTION_BLOGACTION_ERR_INVALID_ACTION);
        return false;    
    }
}

?>