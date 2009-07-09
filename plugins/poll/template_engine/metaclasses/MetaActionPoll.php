<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');


define('ACTION_POLL_ERR_NO_POLL_NUMBER',    'ACTION_POLL_ERR_NO_POLL_NUMBER');
define('ACTION_POLL_ERR_NO_LANGUAGE_ID',    'ACTION_POLL_ERR_NO_LANGUAGE_ID');
define('ACTION_POLL_ERR_NOT_EXISTS',        'ACTION_POLL_ERR_NOT_EXISTS');
define('ACTION_POLL_ERR_NOT_VOTABLE',       'ACTION_POLL_ERR_NOT_VOTABLE');
define('ACTION_POLL_ERR_INVALID_VALUE',     'ACTION_POLL_ERR_INVALID_VALUE');
define('ACTION_POLL_ERR_INVLID_MODE',       'ACTION_POLL_ERR_INVLID_MODE');

class MetaActionPoll extends MetaAction
{
    private $m_poll;


    /**
     * Reads the input parameters and vote the poll
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'poll';
        
        if (!isset($p_input['f_poll_nr']) || empty($p_input['f_poll_nr'])) {
            $this->m_error = new PEAR_Error('The poll number is missing.', ACTION_POLL_ERR_NO_POLL_NUMBER);
            return false;
        }
        $this->m_properties['poll_nr'] = $p_input['f_poll_nr'];
        
        if (!isset($p_input['f_poll_language_id']) || empty($p_input['f_poll_language_id'])) {
            $this->m_error = new PEAR_Error('The poll language is missing.', ACTION_POLL_ERR_NO_LANGUAGE_ID);
            return false;
        }
        $this->m_properties['poll_language_id'] = $p_input['f_poll_language_id'];
        
        if ($p_input['f_poll_mode'] !== 'standard' && $p_input['f_poll_mode'] !== 'ajax') {
            $this->m_error = new PEAR_Error('The poll mode parameter is invalid.', ACTION_POLL_ERR_INVLID_MODE);
            return false;
        }
        $this->m_properties['poll_mode'] = $p_input['f_poll_mode'];
        
        $Poll = new Poll($this->m_properties['poll_language_id'], $this->m_properties['poll_nr']);
        
        if (!$Poll->exists()) {
            $this->m_error = new PEAR_Error('Poll does not exists.', ACTION_POLL_ERR_NOT_EXISTS);
            return false;
        }
        
        if (!$Poll->isVotable()) {
            $this->m_error = new PEAR_Error('Poll is not votable.', ACTION_POLL_ERR_NOT_VOTABLE);
            return false; 
             
        } else {
            switch($p_input['f_poll_mode']) {
                case 'ajax':            
                    $allowed_values = $_SESSION['camp_poll_maxvote'][$this->m_properties['poll_nr']][$this->m_properties['poll_language_id']];
                    
                    if (!is_array($allowed_values)) {
                        $this->m_error = new PEAR_Error('Invalid poll voting value.', ACTION_POLL_ERR_INVALID_VALUE);
                        return false;   
                    }
                    
                    foreach ($Poll->getAnswers() as $PollAnswer) {
                        $nr = $PollAnswer->getNumber();
                        
                        if (isset($p_input['f_pollanswer_'.$nr]) && !empty($p_input['f_pollanswer_'.$nr])) {
                            
                            // check if value is valid
                            if (!array_key_exists($p_input['f_pollanswer_'.$nr], $allowed_values[$nr])) {
                                $this->m_error = new PEAR_Error('Invalid poll voting value.', ACTION_POLL_ERR_INVALID_VALUE);
                                return false;   
                            }
                            $this->m_properties['pollanswer_nr'] = $nr;
                            $this->m_properties['value'] = $p_input['f_pollanswer_'.$nr];
                            break;
                        }
                    }
                    if (!$this->m_properties['value']) {
                        $this->m_error = new PEAR_Error('No answer value was given.', ACTION_POLL_ERR_NOANSWER_VALUE);
                        return false;
                    }
                break;
                
                case 'standard':
                    if (!isset($p_input['f_pollanswer_nr']) || empty($p_input['f_pollanswer_nr'])) {
                        $this->m_error = new PEAR_Error('Invalid poll voting value.', ACTION_POLL_ERR_INVALID_VALUE);
                        return false;
                    }
                    $this->m_properties['pollanswer_nr'] = $p_input['f_pollanswer_nr'];
                    $this->m_properties['value'] = 1;
                break;
            }
        }
        
        $this->m_poll = $Poll;
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {
        if (!is_object($this->m_poll)) {
            return false;   
        }
        
        // vote the poll
        $PollAnswer = new PollAnswer($this->m_properties['poll_language_id'], 
                                     $this->m_properties['poll_nr'],
                                     $this->m_properties['pollanswer_nr']);
        $PollAnswer->vote($this->m_properties['value']);
        
        // reset the f_pollanswer(_$nr) context vars             
        $p_context->default_url->reset_parameter('f_pollanswer_nr');
        $p_context->url->reset_parameter('f_pollanswer_nr');

        foreach ($this->m_poll->getAnswers() as $PollAnswer) {
            $nr = $PollAnswer->getNumber();
            $p_context->default_url->reset_parameter('f_pollanswer_'.$nr);
            $p_context->url->reset_parameter('f_pollanswer_'.$nr);
        }

        $this->m_error = ACTION_OK;       
        return true;
    }
}

?>