<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');

define('ACTION_INTERVIEWITEMSTATUS_ERR_INVALID', 'ACTION_INTERVIEWITEMSTATUS_ERR_INVALID');
define('ACTION_INTERVIEWITEMSTATUS_ERR_NO_PERMISSION', 'ACTION_INTERVIEWITEMSTATUS_ERR_NO_PERMISSION');


class MetaActionInterviewitemstatus extends MetaAction
{
    private $m_interviewitem;


    /**
     * Reads the input parameters and sets up the interviewitem action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_name = 'interviewitemstatus';
        $this->m_defined = true;
        
        switch ($p_input['f_interviewitemstatus']) {
            case 'draft':
            case 'pending':
            case 'public':
            case 'offline':
            case 'delete':
                $this->m_interviewitem = new InterviewItem($p_input['f_interviewitem_id']);
                $this->m_properties['status'] = $p_input['f_interviewitemstatus'];    
            break;
            
            default:
                $this->m_error = new Pear_Error('The given interviewitem status is not defined.', ACTION_INTERVIEWITEMSTATUS_ERR_INVALID);
            break;
        }
        
        $this->m_interviewitem = new InterviewItem(null, $p_input['f_interviewitem_id']);
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
        
        $User = $p_context->user;
        if (!$User->has_permission('plugin_interview_admin') && !$User->has_permission('plugin_interview_moderator')) {
            $this->m_error = new PEAR_Error('User have no permission to maintain this interview item.', ACTION_INTERVIEWITEMSTATUS_ERR_NO_PERMISSION);
            return false;   
        }
        
        if ($this->m_properties['status'] === 'delete') {
            
            $_REQUEST['f_interviewitem_id'] = null;
            $_REQUEST['f_interview_id'] = $this->m_interviewitem->getProperty('fk_interview_id');
            $this->m_interviewitem->delete();
            $this->m_error = ACTION_OK;
            return true;  
            
        } elseif ($this->m_interviewitem->setProperty('status', $this->m_properties['status'])) {
                                
            $this->m_error = ACTION_OK;
            return true;   
        }
        return false;
    }
}

?>