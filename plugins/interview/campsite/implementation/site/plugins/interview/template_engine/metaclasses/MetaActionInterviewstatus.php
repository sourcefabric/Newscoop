<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');

define('ACTION_INTERVIEWSTATUS_ERR_INVALID', 'ACTION_INTERVIEWSTATUS_ERR_INVALID');
define('ACTION_INTERVIEWSTATUS_ERR_NO_PERMISSION', 'ACTION_INTERVIEWSTATUS_ERR_NO_PERMISSION');


class MetaActionInterviewstatus extends MetaAction
{
    private $m_interview;


    /**
     * Reads the input parameters and sets up the interview action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_name = 'interviewstatus';
        $this->m_defined = true;
        
        switch ($p_input['f_interviewstatus']) {
            case 'draft':
            case 'pending':
            case 'published':
            case 'offline':
            case 'delete':
                $this->m_interview = new Interview($p_input['f_interview_id']);
                $this->m_properties['status'] = $p_input['f_interviewstatus'];    
            break;
            
            default:
                $this->m_error = new Pear_Error('The given interview status is not defined.', ACTION_INTERVIEWSTATUS_ERR_INVALID);
            break;
        }
        
        $this->m_interview = new Interview($p_input['f_interview_id']);
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {        
        if (!is_object($this->m_interview)) {
            return false;   
        } 
        
        $User = $p_context->user;
        if (!$User->has_permission('plugin_interview_admin') && !$User->has_permission('plugin_interview_moderator')) {
            $this->m_error = new PEAR_Error('User have no permission to maintain interviews.', ACTION_INTERVIEWSTATUS_ERR_NO_PERMISSION);
            return false;   
        }
        
        if ($this->m_properties['status'] === 'delete') {
            
            $this->m_interview->delete();
            $_REQUEST['f_interview_id'] = null;
            $this->m_error = ACTION_OK;
            return true;
            
        } elseif ($this->m_interview->setProperty('status', $this->m_properties['status'])) {
            
            if ($this->m_properties['status'] == 'pending') {
                // cannot send the invitation right now, 
                // because the context is not initialized.
                // The invitation send action is called from CampPlugin later
                Interview::TriggerSendInvitation();    
            }
            
            $this->m_error = ACTION_OK;
            return true;   
        }
        return false;
    }
}

?>