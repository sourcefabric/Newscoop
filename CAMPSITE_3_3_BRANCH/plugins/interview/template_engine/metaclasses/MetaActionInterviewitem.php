<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');

define('ACTION_INTERVIEWITEM_ERR_NO_QUESTION', 'ACTION_INTERVIEWITEM_ERR_NO_QUESTION');
define('ACTION_INTERVIEWITEM_ERR_NO_ANSWER', 'ACTION_INTERVIEWITEM_ERR_NO_ANSWER');
define('ACTION_INTERVIEWITEM_ERR_NO_STATUS', 'ACTION_INTERVIEWITEM_ERR_NO_STATUS');
define('ACTION_INTERVIEWITEM_ERR_NO_USER', 'ACTION_INTERVIEWITEM_ERR_NO_USER');


class MetaActionInterviewitem extends MetaAction
{
    private $m_interviewitem;
    
    /**
     * Reads the input parameters and sets up the interview action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_name = 'interviewitem';
        $this->m_defined = true;
        
        if (isset($p_input['f_interviewitem_question'])) {
            $this->m_properties['question'] = $p_input['f_interviewitem_question'];
        }
        
        if (isset($p_input['f_interviewitem_answer'])) {
            $this->m_properties['answer'] = $p_input['f_interviewitem_answer'];
        }
        
        if (isset($p_input['f_interviewitem_status'])) {
            $this->m_properties['status'] = $p_input['f_interviewitem_status'];
        }
        
        $this->m_interviewitem = new InterviewItem($p_input['f_interview_id'], $p_input['f_interviewitem_id']);
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {        
        if (!is_object($this->m_interviewitem)) {
            return false;   
        }
        
        if (!$p_context->user->defined) {
            $this->m_error = new PEAR_Error('User must be logged in to add interview question.', ACTION_INTERVIEWITEM_ERR_NO_USER);
            return false;   
        }
               
        if ($this->m_interviewitem->exists()) {
            // to edit existing interviewitem, check privileges 
            $MetaInterview = new MetaInterview($this->m_interviewitem->getProperty('fk_interview_id'));
            
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
                $this->m_interviewitem->setProperty('answer', $this->m_properties['answer']);
                $this->m_interviewitem->setProperty('status', 'published');
            }
            
            if ($is_moderator) {
                if (isset($this->m_properties['question'])) {    
                    $this->m_interviewitem->setProperty('question', $this->m_properties['question']);
                }
                
                if (isset($this->m_properties['answer'])) {    
                    $this->m_interviewitem->setProperty('answer', $this->m_properties['answer']);
                }    
    
                if (isset($this->m_properties['status']) && ($is_admin || $is_moderator)) {    
                    $this->m_interviewitem->setProperty('status', $this->m_properties['status']);
                }  
            }
            
            if ($is_admin) {
                if (isset($this->m_properties['question'])) {    
                    $this->m_interviewitem->setProperty('question', $this->m_properties['question']);
                }
                
                if (isset($this->m_properties['answer'])) {    
                    $this->m_interviewitem->setProperty('answer', $this->m_properties['answer']);
                }    
    
                if (isset($this->m_properties['status']) && ($is_admin || $is_moderator)) {    
                    $this->m_interviewitem->setProperty('status', $this->m_properties['status']);
                }
            }
            
            $this->m_error = ACTION_OK;
            return true;
            
        } else {
            // create new interviewitem (by user, question only)
            
            if (!strlen($this->m_properties['question'])) {
                $this->m_error = new PEAR_Error('An questions was not given.', ACTION_INTERVIEWITEM_ERR_NO_QUESTION);
                return false;
            }
            if ($this->m_interviewitem->create($p_context->user->identifier, $this->m_properties['question'])) {
                $this->m_error = ACTION_OK;
                return true;   
            }   
            
        }
        return false;
    }
}

?>