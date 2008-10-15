<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');

define('ACTION_BLOGENTRY_ERR_NO_TITLE', 'ACTION_BLOGENTRY_ERR_NO_TITLE');
define('ACTION_BLOGENTRY_ERR_NO_CONTENT', 'ACTION_BLOGENTRY_ERR_NO_CONTENT');
define('ACTION_BLOGENTRY_ERR_INVALID_BLOG', 'ACTION_BLOGENTRY_ERR_INVALID_BLOG');


class MetaActionBlogentry extends MetaAction
{
    private $m_blogentry;
    
    /**
     * Reads the input parameters and sets up the blogentry action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_name = 'blogentry';
        $this->m_defined = true;

        if (isset($p_input['f_blog_id'])) {
            $this->m_properties['blog_id'] = $p_input['f_blog_id'];
        }
        
        if (isset($p_input['f_blogentry_title'])) {
            $this->m_properties['title'] = $p_input['f_blogentry_title'];
        }
        
        if (isset($p_input['f_blogentry_content'])) {
            $this->m_properties['content'] = $p_input['f_blogentry_content'];
        }
        
        if (isset($p_input['f_blogentry_mood'])) {
            $this->m_properties['mood'] = $p_input['f_blogentry_mood'];
        }
        
        $this->m_blogentry = new BlogEntry($p_input['f_blogentry_id']);
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {     
        $Blog = new Blog($this->m_properties['blog_id']);   
        
        if (!$Blog->exists()) {
                $this->m_error = new PEAR_Error('None or invalid blog was given.', ACTION_BLOGENTRY_ERR_INVALID_BLOG);
                return false;
        }
        
        /*
        if (!$p_context->user->defined) {
            $this->m_error = new PEAR_Error('User must be logged in to add interview question.', ACTION_INTERVIEWITEM_ERR_NO_USER);
            return false;   
        }
        */
               
        if ($this->m_blogentry->exists()) {
            /*
            // to edit existing blogentry, check privileges 
            $MetaInterview = new MetaInterview($this->m_blogentry->getProperty('fk_interview_id'));
            
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
                $this->m_blogentry->setProperty('answer', $this->m_properties['answer']);
                $this->m_blogentry->setProperty('status', 'published');
            }
            
            if ($is_moderator) {
                if (isset($this->m_properties['question'])) {    
                    $this->m_blogentry->setProperty('question', $this->m_properties['question']);
                }
                
                if (isset($this->m_properties['answer'])) {    
                    $this->m_blogentry->setProperty('answer', $this->m_properties['answer']);
                }    
    
                if (isset($this->m_properties['status']) && ($is_admin || $is_moderator)) {    
                    $this->m_blogentry->setProperty('status', $this->m_properties['status']);
                }  
            }
            
            if ($is_admin) {
                if (isset($this->m_properties['question'])) {    
                    $this->m_blogentry->setProperty('question', $this->m_properties['question']);
                }
                
                if (isset($this->m_properties['answer'])) {    
                    $this->m_blogentry->setProperty('answer', $this->m_properties['answer']);
                }    
    
                if (isset($this->m_properties['status']) && ($is_admin || $is_moderator)) {    
                    $this->m_blogentry->setProperty('status', $this->m_properties['status']);
                }
            }
            
            $this->m_error = ACTION_OK;
            return true;
            */
        } else {
            // create new blogentry

            if (!strlen($this->m_properties['title'])) {
                $this->m_error = new PEAR_Error('No entry title was given.', ACTION_BLOGENTRY_ERR_NO_TITLE);
                return false;
            }
            
            if (!strlen($this->m_properties['content'])) {
                $this->m_error = new PEAR_Error('No entry was given.', ACTION_BLOGENTRY_ERR_NO_CONTENT);
                return false;
            }
            
            if ($this->m_blogentry->create($Blog->getId(), 
                                           $p_context->user->identifier, 
                                           $this->m_properties['title'], 
                                           $this->m_properties['content'],
                                           null, 
                                           $this->m_properties['mood'])) {
                //$_REQUEST['f_blogentry_id'] = $this->m_blogentry->identifier;
                $this->m_error = ACTION_OK;
                return true;   
            }   
            
        }
        return false;
    }
}

?>