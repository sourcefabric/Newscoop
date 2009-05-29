<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');


define('ACTION_INTERVIEW_NOTIFY_INVALID_ACTION', 'ACTION_INTERVIEW_NOTIFY_INVALID_ACTION');

class MetaActionInterviewNotify extends MetaAction
{
    private $m_interview;


    /**
     * Reads the input parameters and sets up the interview action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_name = 'interviewnotify';
        $this->m_defined = true;
        
        if ($p_input['f_interviewnotify'] != 'on' && $p_input['f_interviewnotify'] != 'off') {
            $this->m_error = new PEAR_Error('Invalid action.', ACTION_INTERVIEW_NOTIFY_INVALID_ACTION);
            return;
        }
        $this->m_properties['action'] = $p_input['f_interviewnotify'];
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {   
        $User = new User($p_context->user->identifier);
        
        if ($this->m_properties['action'] == 'on') {  
            $User->setPermission('plugin_interview_notify', true);
            
            $p_context->user = new MetaUser($p_context->user->identifier); // reload MetaUser object because it was modified
            $this->m_error = ACTION_OK;
            return true;
            
        } elseif ($this->m_properties['action'] == 'off') {
            $User->setPermission('plugin_interview_notify', false);
            
            $p_context->user = new MetaUser($p_context->user->identifier); // reload MetaUser object because it was modified
            $this->m_error = ACTION_OK;
            return true;
                        
        }
        return false;
    }
}

?>