<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');

define('ACTION_INTERVIEWSTATUS_ERR_INVALID', 'ACTION_INTERVIEWSTATUS_ERR_INVALID');
define('ACTION_INTERVIEWSTATUS_ERR_NO_PERMISSION', 'ACTION_INTERVIEWSTATUS_ERR_NO_PERMISSION');
define('ACTION_INTERVIEWSTATUS_ERR_NO_USER', 'ACTION_INTERVIEWSTATUS_ERR_NO_USER');
define('ACTION_INTERVIEWSTATUS_ERR_INVITATION_ALREADY_SENT', 'ACTION_INTERVIEWSTATUS_ERR_INVITATION_ALREADY_SENT');

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
            case 'rejected':
            case 'delete':
            case 'activate':
                $this->m_interview = new Interview($p_input['f_interview_id']);
                $this->m_properties['status'] = $p_input['f_interviewstatus'];    
            break;
            
            default:
                $this->m_error = new Pear_Error('The requested interview status is not defined.', ACTION_INTERVIEWSTATUS_ERR_INVALID);
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
        
        $MetaUser = $p_context->user;
        
        if (!$MetaUser->defined) {
            $this->m_error = new PEAR_Error('No user logged in to maintain interview.', ACTION_INTERVIEWSTATUS_ERR_NO_USER);
            return false;    
        }

        switch ($this->m_properties['status']) {
            case 'pending':
            case 'activate':
                if ($MetaUser->has_permission('plugin_interview_guest') && $this->m_interview->getProperty('fk_guest_user_id') == $MetaUser->identifier) {
                    $ok = true;
                }
                if ($MetaUser->has_permission('plugin_interview_admin')) {
                    $ok = true;
                }
            break;
            
            case 'draft':
            case 'published':
            case 'rejected':
            case 'delete':
                if ($MetaUser->has_permission('plugin_interview_guest') && $this->m_interview->getProperty('fk_guest_user_id') == $MetaUser->identifier) {
                    $ok = true;
                }
            break;
        }
        
        if (!$ok) {
            $this->m_error = new PEAR_Error('The logged in user have no permission to maintain interviews.', ACTION_INTERVIEWSTATUS_ERR_NO_PERMISSION);
            return false;
        }
        
        switch ($this->m_properties['status']) {
            case 'delete':
                $this->m_interview->delete();
                $_REQUEST['f_interview_id'] = null;
            break;
            
            case 'draft':       
            case 'pending':
            case 'published':
            case 'rejected':
                $this->m_interview->setProperty('status', $this->m_properties['status']);
            break;
            
            case 'activate':
                if ($datetime = $this->m_interview->getProperty('questioneer_invitation_sent')) {
                    $this->m_error = new PEAR_Error('Invitations to questioneers has already been sent on '.$datetime.'.', ACTION_INTERVIEWSTATUS_ERR_INVITATION_ALREADY_SENT);
                    return false;     
                }
                $this->m_interview->setProperty('status', 'pending');
                $this->m_interview->sendQuestioneerInvitation();
            break;   
        }
        
        $this->m_error = ACTION_OK;
        return true;
    }
}

?>